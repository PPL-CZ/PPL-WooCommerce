<?php

namespace PPLCZVendor\Jane\Component\JsonSchema\Guesser\JsonSchema;

use PPLCZVendor\Jane\Component\JsonSchema\Guesser\ChainGuesserAwareInterface;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\ChainGuesserAwareTrait;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\ClassGuesserInterface;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\MapType;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\Guess\Type;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\GuesserInterface;
use PPLCZVendor\Jane\Component\JsonSchema\Guesser\TypeGuesserInterface;
use PPLCZVendor\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLCZVendor\Jane\Component\JsonSchema\Registry\Registry;
class AdditionalPropertiesGuesser implements GuesserInterface, TypeGuesserInterface, ChainGuesserAwareInterface, ClassGuesserInterface
{
    use ChainGuesserAwareTrait;
    /**
     * {@inheritdoc}
     */
    public function guessClass($object, string $name, string $reference, Registry $registry) : void
    {
        if (\is_a($object->getAdditionalProperties(), $this->getSchemaClass())) {
            $this->chainGuesser->guessClass($object->getAdditionalProperties(), $name . 'Item', $reference . '/additionalProperties', $registry);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function supportObject($object) : bool
    {
        $class = $this->getSchemaClass();
        if (!$object instanceof $class) {
            return \false;
        }
        if ('object' !== $object->getType()) {
            return \false;
        }
        if (\true !== $object->getAdditionalProperties() && !\is_object($object->getAdditionalProperties())) {
            return \false;
        }
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function guessType($object, string $name, string $reference, Registry $registry) : Type
    {
        if (\true === $object->getAdditionalProperties()) {
            return new MapType($object, new Type($object, 'mixed'));
        }
        return new MapType($object, $this->chainGuesser->guessType($object->getAdditionalProperties(), $name . 'Item', $reference . '/additionalProperties', $registry));
    }
    protected function getSchemaClass() : string
    {
        return JsonSchema::class;
    }
}
