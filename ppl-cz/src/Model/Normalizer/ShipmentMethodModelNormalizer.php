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
class ShipmentMethodModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\ShipmentMethodModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\ShipmentMethodModel';
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
        $object = new \PPLCZ\Model\Model\ShipmentMethodModel();
        if (\array_key_exists('maxWeight', $data) && \is_int($data['maxWeight'])) {
            $data['maxWeight'] = (double) $data['maxWeight'];
        }
        if (\array_key_exists('maxPackages', $data) && \is_int($data['maxPackages'])) {
            $data['maxPackages'] = (double) $data['maxPackages'];
        }
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('code', $data)) {
            $object->setCode($data['code']);
            unset($data['code']);
        }
        if (\array_key_exists('title', $data)) {
            $object->setTitle($data['title']);
            unset($data['title']);
        }
        if (\array_key_exists('description', $data)) {
            $object->setDescription($data['description']);
            unset($data['description']);
        }
        if (\array_key_exists('ageValidation', $data) && $data['ageValidation'] !== null) {
            $object->setAgeValidation($data['ageValidation']);
            unset($data['ageValidation']);
        }
        elseif (\array_key_exists('ageValidation', $data) && $data['ageValidation'] === null) {
            $object->setAgeValidation(null);
        }
        if (\array_key_exists('codAvailable', $data)) {
            $object->setCodAvailable($data['codAvailable']);
            unset($data['codAvailable']);
        }
        if (\array_key_exists('parcelRequired', $data)) {
            $object->setParcelRequired($data['parcelRequired']);
            unset($data['parcelRequired']);
        }
        if (\array_key_exists('disabledParcelTypes', $data) && $data['disabledParcelTypes'] !== null) {
            $values = array();
            foreach ($data['disabledParcelTypes'] as $value) {
                $values[] = $value;
            }
            $object->setDisabledParcelTypes($values);
            unset($data['disabledParcelTypes']);
        }
        elseif (\array_key_exists('disabledParcelTypes', $data) && $data['disabledParcelTypes'] === null) {
            $object->setDisabledParcelTypes(null);
        }
        if (\array_key_exists('availableParcelTypes', $data) && $data['availableParcelTypes'] !== null) {
            $values_1 = array();
            foreach ($data['availableParcelTypes'] as $value_1) {
                $values_1[] = $value_1;
            }
            $object->setAvailableParcelTypes($values_1);
            unset($data['availableParcelTypes']);
        }
        elseif (\array_key_exists('availableParcelTypes', $data) && $data['availableParcelTypes'] === null) {
            $object->setAvailableParcelTypes(null);
        }
        if (\array_key_exists('countries', $data)) {
            $values_2 = array();
            foreach ($data['countries'] as $value_2) {
                $values_2[] = $value_2;
            }
            $object->setCountries($values_2);
            unset($data['countries']);
        }
        if (\array_key_exists('maxWeight', $data) && $data['maxWeight'] !== null) {
            $object->setMaxWeight($data['maxWeight']);
            unset($data['maxWeight']);
        }
        elseif (\array_key_exists('maxWeight', $data) && $data['maxWeight'] === null) {
            $object->setMaxWeight(null);
        }
        if (\array_key_exists('maxDimension', $data) && $data['maxDimension'] !== null) {
            $values_3 = array();
            foreach ($data['maxDimension'] as $value_3) {
                $values_3[] = $value_3;
            }
            $object->setMaxDimension($values_3);
            unset($data['maxDimension']);
        }
        elseif (\array_key_exists('maxDimension', $data) && $data['maxDimension'] === null) {
            $object->setMaxDimension(null);
        }
        if (\array_key_exists('maxPackages', $data) && $data['maxPackages'] !== null) {
            $object->setMaxPackages($data['maxPackages']);
            unset($data['maxPackages']);
        }
        elseif (\array_key_exists('maxPackages', $data) && $data['maxPackages'] === null) {
            $object->setMaxPackages(null);
        }
        foreach ($data as $key => $value_4) {
            if (preg_match('/.*/', (string) $key)) {
                $object[$key] = $value_4;
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
        $data['code'] = $object->getCode();
        $data['title'] = $object->getTitle();
        $data['description'] = $object->getDescription();
        $data['ageValidation'] = $object->getAgeValidation();
        $data['codAvailable'] = $object->getCodAvailable();
        $data['parcelRequired'] = $object->getParcelRequired();
        if ($object->isInitialized('disabledParcelTypes') && null !== $object->getDisabledParcelTypes()) {
            $values = array();
            foreach ($object->getDisabledParcelTypes() as $value) {
                $values[] = $value;
            }
            $data['disabledParcelTypes'] = $values;
        }
        if ($object->isInitialized('availableParcelTypes') && null !== $object->getAvailableParcelTypes()) {
            $values_1 = array();
            foreach ($object->getAvailableParcelTypes() as $value_1) {
                $values_1[] = $value_1;
            }
            $data['availableParcelTypes'] = $values_1;
        }
        $values_2 = array();
        foreach ($object->getCountries() as $value_2) {
            $values_2[] = $value_2;
        }
        $data['countries'] = $values_2;
        $data['maxWeight'] = $object->getMaxWeight();
        if ($object->isInitialized('maxDimension') && null !== $object->getMaxDimension()) {
            $values_3 = array();
            foreach ($object->getMaxDimension() as $value_3) {
                $values_3[] = $value_3;
            }
            $data['maxDimension'] = $values_3;
        }
        $data['maxPackages'] = $object->getMaxPackages();
        foreach ($object as $key => $value_4) {
            if (preg_match('/.*/', (string) $key)) {
                $data[$key] = $value_4;
            }
        }
        return $data;
    }
    public function getSupportedTypes(?string $format = null) : ?array
    {
        return array('PPLCZ\\Model\\Model\\ShipmentMethodModel' => false);
    }
}