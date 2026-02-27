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
class ParcelDataModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\ParcelDataModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\ParcelDataModel';
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
        $object = new \PPLCZ\Model\Model\ParcelDataModel();
        if (\array_key_exists('id', $data) && \is_int($data['id'])) {
            $data['id'] = (double) $data['id'];
        }
        if (\array_key_exists('rnd', $data) && \is_int($data['rnd'])) {
            $data['rnd'] = (double) $data['rnd'];
        }
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('name', $data) && $data['name'] !== null) {
            $object->setName($data['name']);
            unset($data['name']);
        }
        elseif (\array_key_exists('name', $data) && $data['name'] === null) {
            $object->setName(null);
        }
        if (\array_key_exists('activeCardPayment', $data) && $data['activeCardPayment'] !== null) {
            $object->setActiveCardPayment($data['activeCardPayment']);
            unset($data['activeCardPayment']);
        }
        elseif (\array_key_exists('activeCardPayment', $data) && $data['activeCardPayment'] === null) {
            $object->setActiveCardPayment(null);
        }
        if (\array_key_exists('activeCashPayment', $data) && $data['activeCashPayment'] !== null) {
            $object->setActiveCashPayment($data['activeCashPayment']);
            unset($data['activeCashPayment']);
        }
        elseif (\array_key_exists('activeCashPayment', $data) && $data['activeCashPayment'] === null) {
            $object->setActiveCashPayment(null);
        }
        if (\array_key_exists('code', $data) && $data['code'] !== null) {
            $object->setCode($data['code']);
            unset($data['code']);
        }
        elseif (\array_key_exists('code', $data) && $data['code'] === null) {
            $object->setCode(null);
        }
        if (\array_key_exists('country', $data) && $data['country'] !== null) {
            $object->setCountry($data['country']);
            unset($data['country']);
        }
        elseif (\array_key_exists('country', $data) && $data['country'] === null) {
            $object->setCountry(null);
        }
        if (\array_key_exists('openHours', $data) && $data['openHours'] !== null) {
            $values = array();
            foreach ($data['openHours'] as $value) {
                $values[] = $value;
            }
            $object->setOpenHours($values);
            unset($data['openHours']);
        }
        elseif (\array_key_exists('openHours', $data) && $data['openHours'] === null) {
            $object->setOpenHours(null);
        }
        if (\array_key_exists('street', $data) && $data['street'] !== null) {
            $object->setStreet($data['street']);
            unset($data['street']);
        }
        elseif (\array_key_exists('street', $data) && $data['street'] === null) {
            $object->setStreet(null);
        }
        if (\array_key_exists('city', $data) && $data['city'] !== null) {
            $object->setCity($data['city']);
            unset($data['city']);
        }
        elseif (\array_key_exists('city', $data) && $data['city'] === null) {
            $object->setCity(null);
        }
        if (\array_key_exists('zipCode', $data) && $data['zipCode'] !== null) {
            $object->setZipCode($data['zipCode']);
            unset($data['zipCode']);
        }
        elseif (\array_key_exists('zipCode', $data) && $data['zipCode'] === null) {
            $object->setZipCode(null);
        }
        if (\array_key_exists('id', $data) && $data['id'] !== null) {
            $object->setId($data['id']);
            unset($data['id']);
        }
        elseif (\array_key_exists('id', $data) && $data['id'] === null) {
            $object->setId(null);
        }
        if (\array_key_exists('rnd', $data) && $data['rnd'] !== null) {
            $object->setRnd($data['rnd']);
            unset($data['rnd']);
        }
        elseif (\array_key_exists('rnd', $data) && $data['rnd'] === null) {
            $object->setRnd(null);
        }
        if (\array_key_exists('accessPointType', $data) && $data['accessPointType'] !== null) {
            $object->setAccessPointType($data['accessPointType']);
            unset($data['accessPointType']);
        }
        elseif (\array_key_exists('accessPointType', $data) && $data['accessPointType'] === null) {
            $object->setAccessPointType(null);
        }
        if (\array_key_exists('gps', $data) && $data['gps'] !== null) {
            $object->setGps($this->denormalizer->denormalize($data['gps'], 'PPLCZ\\Model\\Model\\ParcelDataModelGps', 'json', $context));
            unset($data['gps']);
        }
        elseif (\array_key_exists('gps', $data) && $data['gps'] === null) {
            $object->setGps(null);
        }
        if (\array_key_exists('posnRequired', $data) && $data['posnRequired'] !== null) {
            $object->setPosnRequired($data['posnRequired']);
            unset($data['posnRequired']);
        }
        elseif (\array_key_exists('posnRequired', $data) && $data['posnRequired'] === null) {
            $object->setPosnRequired(null);
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
        if ($object->isInitialized('name') && null !== $object->getName()) {
            $data['name'] = $object->getName();
        }
        if ($object->isInitialized('activeCardPayment') && null !== $object->getActiveCardPayment()) {
            $data['activeCardPayment'] = $object->getActiveCardPayment();
        }
        if ($object->isInitialized('activeCashPayment') && null !== $object->getActiveCashPayment()) {
            $data['activeCashPayment'] = $object->getActiveCashPayment();
        }
        if ($object->isInitialized('code') && null !== $object->getCode()) {
            $data['code'] = $object->getCode();
        }
        if ($object->isInitialized('country') && null !== $object->getCountry()) {
            $data['country'] = $object->getCountry();
        }
        if ($object->isInitialized('openHours') && null !== $object->getOpenHours()) {
            $values = array();
            foreach ($object->getOpenHours() as $value) {
                $values[] = $value;
            }
            $data['openHours'] = $values;
        }
        if ($object->isInitialized('street') && null !== $object->getStreet()) {
            $data['street'] = $object->getStreet();
        }
        if ($object->isInitialized('city') && null !== $object->getCity()) {
            $data['city'] = $object->getCity();
        }
        if ($object->isInitialized('zipCode') && null !== $object->getZipCode()) {
            $data['zipCode'] = $object->getZipCode();
        }
        if ($object->isInitialized('id') && null !== $object->getId()) {
            $data['id'] = $object->getId();
        }
        if ($object->isInitialized('rnd') && null !== $object->getRnd()) {
            $data['rnd'] = $object->getRnd();
        }
        if ($object->isInitialized('accessPointType') && null !== $object->getAccessPointType()) {
            $data['accessPointType'] = $object->getAccessPointType();
        }
        if ($object->isInitialized('gps') && null !== $object->getGps()) {
            $data['gps'] = $this->normalizer->normalize($object->getGps(), 'json', $context);
        }
        if ($object->isInitialized('posnRequired') && null !== $object->getPosnRequired()) {
            $data['posnRequired'] = $object->getPosnRequired();
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
        return array('PPLCZ\\Model\\Model\\ParcelDataModel' => false);
    }
}