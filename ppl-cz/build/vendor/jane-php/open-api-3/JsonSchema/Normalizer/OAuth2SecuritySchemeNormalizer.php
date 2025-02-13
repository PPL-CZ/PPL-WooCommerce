<?php

namespace PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Normalizer;

use PPLCZVendor\Jane\Component\JsonSchemaRuntime\Reference;
use PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Runtime\Normalizer\CheckArray;
use PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Runtime\Normalizer\ValidatorTrait;
use PPLCZVendor\Symfony\Component\Serializer\Exception\InvalidArgumentException;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class OAuth2SecuritySchemeNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, $context = []) : bool
    {
        return $type === 'Jane\\Component\\OpenApi3\\JsonSchema\\Model\\OAuth2SecurityScheme';
    }
    public function supportsNormalization($data, $format = null, $context = []) : bool
    {
        return $data instanceof \PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Model\OAuth2SecurityScheme;
    }
    /**
     * @return mixed
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (isset($data['$ref'])) {
            return new Reference($data['$ref'], $context['document-origin']);
        }
        if (isset($data['$recursiveRef'])) {
            return new Reference($data['$recursiveRef'], $context['document-origin']);
        }
        $object = new \PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Model\OAuth2SecurityScheme();
        if (null === $data || \false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('type', $data) && $data['type'] !== null) {
            $object->setType($data['type']);
            unset($data['type']);
        } elseif (\array_key_exists('type', $data) && $data['type'] === null) {
            $object->setType(null);
        }
        if (\array_key_exists('flows', $data) && $data['flows'] !== null) {
            $object->setFlows($this->denormalizer->denormalize($data['flows'], 'PPLCZVendor\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\OAuthFlows', 'json', $context));
            unset($data['flows']);
        } elseif (\array_key_exists('flows', $data) && $data['flows'] === null) {
            $object->setFlows(null);
        }
        if (\array_key_exists('description', $data) && $data['description'] !== null) {
            $object->setDescription($data['description']);
            unset($data['description']);
        } elseif (\array_key_exists('description', $data) && $data['description'] === null) {
            $object->setDescription(null);
        }
        foreach ($data as $key => $value) {
            if (\preg_match('/^x-/', (string) $key)) {
                $object[$key] = $value;
            }
        }
        return $object;
    }
    /**
     * @return array|string|int|float|bool|\ArrayObject|null
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $data = array();
        $data['type'] = $object->getType();
        $data['flows'] = $this->normalizer->normalize($object->getFlows(), 'json', $context);
        if (null !== $object->getDescription()) {
            $data['description'] = $object->getDescription();
        }
        foreach ($object as $key => $value) {
            if (\preg_match('/^x-/', (string) $key)) {
                $data[$key] = $value;
            }
        }
        return $data;
    }
}
