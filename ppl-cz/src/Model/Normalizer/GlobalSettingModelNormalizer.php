<?php

namespace PPLCZ\Model\Normalizer;

use PPLCZVendor\Jane\Component\JsonSchemaRuntime\Reference;
use PPLCZ\Model\Runtime\Normalizer\CheckArray;
use PPLCZ\Model\Runtime\Normalizer\ValidatorTrait;
use PPLCZVendor\Symfony\Component\Serializer\Exception\InvalidArgumentException;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class GlobalSettingModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\GlobalSettingModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\GlobalSettingModel';
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
        $object = new \PPLCZ\Model\Model\GlobalSettingModel();
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('useOrderNumberInPackages', $data)) {
            $object->setUseOrderNumberInPackages($data['useOrderNumberInPackages']);
            unset($data['useOrderNumberInPackages']);
        }
        if (\array_key_exists('useOrderNumberInVariableSymbol', $data)) {
            $object->setUseOrderNumberInVariableSymbol($data['useOrderNumberInVariableSymbol']);
            unset($data['useOrderNumberInVariableSymbol']);
        }
        if (\array_key_exists('map', $data)) {
            $object->setMap($this->denormalizer->denormalize($data['map'], 'PPLCZ\\Model\\Model\\GlobalSettingMapModel', 'json', $context));
            unset($data['map']);
        }
        foreach ($data as $key => $value) {
            if (preg_match('/.*/', (string) $key)) {
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
        if ($object->isInitialized('useOrderNumberInPackages') && null !== $object->getUseOrderNumberInPackages()) {
            $data['useOrderNumberInPackages'] = $object->getUseOrderNumberInPackages();
        }
        if ($object->isInitialized('useOrderNumberInVariableSymbol') && null !== $object->getUseOrderNumberInVariableSymbol()) {
            $data['useOrderNumberInVariableSymbol'] = $object->getUseOrderNumberInVariableSymbol();
        }
        if ($object->isInitialized('map') && null !== $object->getMap()) {
            $data['map'] = $this->normalizer->normalize($object->getMap(), 'json', $context);
        }
        foreach ($object as $key => $value) {
            if (preg_match('/.*/', (string) $key)) {
                $data[$key] = $value;
            }
        }
        return $data;
    }
    public function getSupportedTypes(?string $format = null) : ?array
    {
        return array('PPLCZ\\Model\\Model\\GlobalSettingModel' => false);
    }
}