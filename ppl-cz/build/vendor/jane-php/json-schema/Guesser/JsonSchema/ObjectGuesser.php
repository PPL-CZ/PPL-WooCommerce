<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser\JsonSchema;

use PPLCZVendor\Jane\Component\JsonSchema\Generator\Naming;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\ChainGuesserAwareInterface;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\ChainGuesserAwareTrait;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\ClassGuesserInterface;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\ObjectType;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\Property;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\Type;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\GuesserInterface;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\GuesserResolverTrait;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\PropertiesGuesserInterface;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\TypeGuesserInterface;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ChainValidatorFactory;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Validator\ValidatorInterface;
use PPLCZVendor\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLCZVendor\Jane\Component\JsonSchema\Registry\Registry;
use PPLCZVendor\Jane\Component\JsonSchemaRuntime\Reference;
use PPLCZVendor\Symfony\Component\Serializer\SerializerInterface;
class ObjectGuesser implements GuesserInterface, PropertiesGuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface, ClassGuesserInterface
{
    use ChainGuesserAwareTrait;
    use GuesserResolverTrait;
    /**
     * @var \Jane\Component\JsonSchema\Generator\Naming
     */
    protected $naming;
    /** @var ValidatorInterface */
    protected $chainValidator;
    public function __construct(Naming $naming, SerializerInterface $serializer)
    {
        $this->naming = $naming;
        $this->serializer = $serializer;
    }
    /**
     * {@inheritdoc}
     */
    public function supportObject($object) : bool
    {
        return $object instanceof JsonSchema && (\is_array($object->getType()) ? \in_array('object', $object->getType()) : 'object' === $object->getType()) && null !== $object->getProperties();
    }
    /**
     * {@inheritdoc}
     *
     * @param JsonSchema $object
     */
    public function guessClass($object, string $name, string $reference, Registry $registry) : void
    {
        if (!$registry->hasClass($reference)) {
            $this->initChainValidator($registry);
            $extensions = [];
            if ($object->getAdditionalProperties()) {
                $extensionObject = null;
                if (\is_object($object->getAdditionalProperties())) {
                    $extensionObject = $object->getAdditionalProperties();
                }
                $extensions['.*'] = ['object' => $extensionObject, 'reference' => $reference . '/additionalProperties'];
            } elseif (\method_exists($object, 'getPatternProperties') && $object->getPatternProperties() !== null) {
                foreach ($object->getPatternProperties() as $pattern => $patternProperty) {
                    $extensions[$pattern] = ['object' => $patternProperty, 'reference' => $reference . '/patternProperties/' . $pattern];
                }
            }
            $classGuess = $this->createClassGuess($object, $reference, $name, $extensions);
            if (null !== $object->getRequired()) {
                $classGuess->setRequired($object->getRequired());
            }
            $schema = $registry->getSchema($reference);
            if (null !== $schema) {
                $schema->addClass($reference, $classGuess);
            }
        }
        foreach ($object->getProperties() as $key => $property) {
            $this->chainGuesser->guessClass($property, $name . \ucfirst($key), $reference . '/properties/' . $key, $registry);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function guessProperties($object, string $name, string $reference, Registry $registry) : array
    {
        /** @var JsonSchema $object */
        $properties = [];
        $this->initChainValidator($registry);
        foreach ($object->getProperties() as $key => $property) {
            $propertyObj = $property;
            if ($propertyObj instanceof Reference) {
                $propertyObj = $this->resolve($propertyObj, $this->getSchemaClass());
            }
            $nullable = $this->isPropertyNullable($propertyObj);
            $required = \false;
            if (\is_array($object->getRequired())) {
                $required = \in_array($key, $object->getRequired());
            }
            $newProperty = new Property($property, $key, $reference . '/properties/' . $key, $nullable, $required, null, $propertyObj->getDescription(), $propertyObj->getDefault(), $propertyObj->getReadOnly());
            if (\method_exists($propertyObj, 'getDeprecated')) {
                $newProperty->setDeprecated($propertyObj->getDeprecated());
            }
            $this->chainValidator->guess($propertyObj, $name, $newProperty);
            $properties[$key] = $newProperty;
        }
        return $properties;
    }
    protected function isPropertyNullable($property) : bool
    {
        $oneOf = $property->getOneOf();
        if (!empty($oneOf)) {
            foreach ($oneOf as $oneOfProperty) {
                if (!$oneOfProperty instanceof JsonSchema) {
                    continue;
                }
                if ($this->isPropertyNullable($oneOfProperty)) {
                    return \true;
                }
            }
            return \false;
        }
        $type = $property->getType();
        return 'null' == $type || \is_array($type) && \in_array('null', $type);
    }
    /**
     * {@inheritdoc}
     */
    public function guessType($object, string $name, string $reference, Registry $registry) : Type
    {
        $discriminants = [];
        $required = $object->getRequired() ?: [];
        foreach ($object->getProperties() as $key => $property) {
            if (!\in_array($key, $required)) {
                continue;
            }
            if ($property instanceof Reference) {
                $property = $this->resolve($property, $this->getSchemaClass());
            }
            if (null !== $property->getEnum()) {
                $isSimple = \true;
                foreach ($property->getEnum() as $value) {
                    if (\is_array($value) || \is_object($value)) {
                        $isSimple = \false;
                    }
                }
                if ($isSimple) {
                    $discriminants[$key] = $property->getEnum();
                }
            } else {
                $discriminants[$key] = null;
            }
        }
        if ($registry->hasClass($reference) && null !== ($schema = $registry->getSchema($reference))) {
            return new ObjectType($object, $registry->getClass($reference)->getName(), $schema->getNamespace(), $discriminants);
        }
        return new Type($object, 'object');
    }
    protected function getSchemaClass() : string
    {
        return JsonSchema::class;
    }
    protected function createClassGuess($object, string $reference, string $name, array $extensions) : ClassGuess
    {
        return new ClassGuess($object, $reference, $this->naming->getClassName($name), $extensions, $object->getDeprecated());
    }
    private function initChainValidator(Registry $registry) : void
    {
        if (null === $this->chainValidator) {
            $this->chainValidator = ChainValidatorFactory::create($this->naming, $registry, $this->serializer);
        }
    }
}
