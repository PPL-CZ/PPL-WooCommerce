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
class PasswordOAuthFlowNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, $context = []) : bool
    {
        return $type === 'Jane\\Component\\OpenApi3\\JsonSchema\\Model\\PasswordOAuthFlow';
    }
    public function supportsNormalization($data, $format = null, $context = []) : bool
    {
        return $data instanceof \PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Model\PasswordOAuthFlow;
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
        $object = new \PPLCZVendor\Jane\Component\OpenApi3\JsonSchema\Model\PasswordOAuthFlow();
        if (null === $data || \false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('tokenUrl', $data) && $data['tokenUrl'] !== null) {
            $object->setTokenUrl($data['tokenUrl']);
            unset($data['tokenUrl']);
        } elseif (\array_key_exists('tokenUrl', $data) && $data['tokenUrl'] === null) {
            $object->setTokenUrl(null);
        }
        if (\array_key_exists('refreshUrl', $data) && $data['refreshUrl'] !== null) {
            $object->setRefreshUrl($data['refreshUrl']);
            unset($data['refreshUrl']);
        } elseif (\array_key_exists('refreshUrl', $data) && $data['refreshUrl'] === null) {
            $object->setRefreshUrl(null);
        }
        if (\array_key_exists('scopes', $data) && $data['scopes'] !== null) {
            $values = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data['scopes'] as $key => $value) {
                $values[$key] = $value;
            }
            $object->setScopes($values);
            unset($data['scopes']);
        } elseif (\array_key_exists('scopes', $data) && $data['scopes'] === null) {
            $object->setScopes(null);
        }
        foreach ($data as $key_1 => $value_1) {
            if (\preg_match('/^x-/', (string) $key_1)) {
                $object[$key_1] = $value_1;
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
        $data['tokenUrl'] = $object->getTokenUrl();
        if (null !== $object->getRefreshUrl()) {
            $data['refreshUrl'] = $object->getRefreshUrl();
        }
        if (null !== $object->getScopes()) {
            $values = array();
            foreach ($object->getScopes() as $key => $value) {
                $values[$key] = $value;
            }
            $data['scopes'] = $values;
        }
        foreach ($object as $key_1 => $value_1) {
            if (\preg_match('/^x-/', (string) $key_1)) {
                $data[$key_1] = $value_1;
            }
        }
        return $data;
    }
}
