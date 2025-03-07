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
class UpdateSyncPhasesModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\UpdateSyncPhasesModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\UpdateSyncPhasesModel';
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
        $object = new \PPLCZ\Model\Model\UpdateSyncPhasesModel();
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('maxSync', $data) && $data['maxSync'] !== null) {
            $object->setMaxSync($data['maxSync']);
            unset($data['maxSync']);
        }
        elseif (\array_key_exists('maxSync', $data) && $data['maxSync'] === null) {
            $object->setMaxSync(null);
        }
        if (\array_key_exists('phases', $data) && $data['phases'] !== null) {
            $values = array();
            foreach ($data['phases'] as $value) {
                $values[] = $this->denormalizer->denormalize($value, 'PPLCZ\\Model\\Model\\UpdateSyncPhasesModelPhasesItem', 'json', $context);
            }
            $object->setPhases($values);
            unset($data['phases']);
        }
        elseif (\array_key_exists('phases', $data) && $data['phases'] === null) {
            $object->setPhases(null);
        }
        foreach ($data as $key => $value_1) {
            if (preg_match('/.*/', (string) $key)) {
                $object[$key] = $value_1;
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
        if ($object->isInitialized('maxSync') && null !== $object->getMaxSync()) {
            $data['maxSync'] = $object->getMaxSync();
        }
        if ($object->isInitialized('phases') && null !== $object->getPhases()) {
            $values = array();
            foreach ($object->getPhases() as $value) {
                $values[] = $this->normalizer->normalize($value, 'json', $context);
            }
            $data['phases'] = $values;
        }
        foreach ($object as $key => $value_1) {
            if (preg_match('/.*/', (string) $key)) {
                $data[$key] = $value_1;
            }
        }
        return $data;
    }
    public function getSupportedTypes(?string $format = null) : ?array
    {
        return array('PPLCZ\\Model\\Model\\UpdateSyncPhasesModel' => false);
    }
}