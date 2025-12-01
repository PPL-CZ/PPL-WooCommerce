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
class PackageSizeModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\PackageSizeModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\PackageSizeModel';
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
        $object = new \PPLCZ\Model\Model\PackageSizeModel();
        if (\array_key_exists('xSize', $data) && \is_int($data['xSize'])) {
            $data['xSize'] = (double) $data['xSize'];
        }
        if (\array_key_exists('ySize', $data) && \is_int($data['ySize'])) {
            $data['ySize'] = (double) $data['ySize'];
        }
        if (\array_key_exists('zSize', $data) && \is_int($data['zSize'])) {
            $data['zSize'] = (double) $data['zSize'];
        }
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('xSize', $data) && $data['xSize'] !== null) {
            $object->setXSize($data['xSize']);
            unset($data['xSize']);
        }
        elseif (\array_key_exists('xSize', $data) && $data['xSize'] === null) {
            $object->setXSize(null);
        }
        if (\array_key_exists('ySize', $data) && $data['ySize'] !== null) {
            $object->setYSize($data['ySize']);
            unset($data['ySize']);
        }
        elseif (\array_key_exists('ySize', $data) && $data['ySize'] === null) {
            $object->setYSize(null);
        }
        if (\array_key_exists('zSize', $data) && $data['zSize'] !== null) {
            $object->setZSize($data['zSize']);
            unset($data['zSize']);
        }
        elseif (\array_key_exists('zSize', $data) && $data['zSize'] === null) {
            $object->setZSize(null);
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
        if ($object->isInitialized('xSize') && null !== $object->getXSize()) {
            $data['xSize'] = $object->getXSize();
        }
        if ($object->isInitialized('ySize') && null !== $object->getYSize()) {
            $data['ySize'] = $object->getYSize();
        }
        if ($object->isInitialized('zSize') && null !== $object->getZSize()) {
            $data['zSize'] = $object->getZSize();
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
        return array('PPLCZ\\Model\\Model\\PackageSizeModel' => false);
    }
}