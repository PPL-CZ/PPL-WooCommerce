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
class ErrorLogModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\ErrorLogModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\ErrorLogModel';
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
        $object = new \PPLCZ\Model\Model\ErrorLogModel();
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('mail', $data) && $data['mail'] !== null) {
            $object->setMail($data['mail']);
            unset($data['mail']);
        }
        elseif (\array_key_exists('mail', $data) && $data['mail'] === null) {
            $object->setMail(null);
        }
        if (\array_key_exists('info', $data) && $data['info'] !== null) {
            $object->setInfo($data['info']);
            unset($data['info']);
        }
        elseif (\array_key_exists('info', $data) && $data['info'] === null) {
            $object->setInfo(null);
        }
        if (\array_key_exists('shipmentsSetting', $data) && $data['shipmentsSetting'] !== null) {
            $values = array();
            foreach ($data['shipmentsSetting'] as $value) {
                $values[] = $this->denormalizer->denormalize($value, 'PPLCZ\\Model\\Model\\ErrorLogShipmentSettingModel', 'json', $context);
            }
            $object->setShipmentsSetting($values);
            unset($data['shipmentsSetting']);
        }
        elseif (\array_key_exists('shipmentsSetting', $data) && $data['shipmentsSetting'] === null) {
            $object->setShipmentsSetting(null);
        }
        if (\array_key_exists('globalParcelSetting', $data)) {
            $object->setGlobalParcelSetting($this->denormalizer->denormalize($data['globalParcelSetting'], 'PPLCZ\\Model\\Model\\ParcelPlacesModel', 'json', $context));
            unset($data['globalParcelSetting']);
        }
        if (\array_key_exists('categorySetting', $data)) {
            $values_1 = array();
            foreach ($data['categorySetting'] as $value_1) {
                $values_1[] = $this->denormalizer->denormalize($value_1, 'PPLCZ\\Model\\Model\\ErrorLogCategorySettingModel', 'json', $context);
            }
            $object->setCategorySetting($values_1);
            unset($data['categorySetting']);
        }
        if (\array_key_exists('productsSetting', $data)) {
            $values_2 = array();
            foreach ($data['productsSetting'] as $value_2) {
                $values_2[] = $this->denormalizer->denormalize($value_2, 'PPLCZ\\Model\\Model\\ErrorLogProductSettingModel', 'json', $context);
            }
            $object->setProductsSetting($values_2);
            unset($data['productsSetting']);
        }
        if (\array_key_exists('orders', $data)) {
            $values_3 = array();
            foreach ($data['orders'] as $value_3) {
                $values_3[] = $value_3;
            }
            $object->setOrders($values_3);
            unset($data['orders']);
        }
        if (\array_key_exists('errors', $data)) {
            $values_4 = array();
            foreach ($data['errors'] as $value_4) {
                $values_4[] = $this->denormalizer->denormalize($value_4, 'PPLCZ\\Model\\Model\\ErrorLogItemModel', 'json', $context);
            }
            $object->setErrors($values_4);
            unset($data['errors']);
        }
        foreach ($data as $key => $value_5) {
            if (preg_match('/.*/', (string) $key)) {
                $object[$key] = $value_5;
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
        if ($object->isInitialized('mail') && null !== $object->getMail()) {
            $data['mail'] = $object->getMail();
        }
        if ($object->isInitialized('info') && null !== $object->getInfo()) {
            $data['info'] = $object->getInfo();
        }
        if ($object->isInitialized('shipmentsSetting') && null !== $object->getShipmentsSetting()) {
            $values = array();
            foreach ($object->getShipmentsSetting() as $value) {
                $values[] = $this->normalizer->normalize($value, 'json', $context);
            }
            $data['shipmentsSetting'] = $values;
        }
        if ($object->isInitialized('globalParcelSetting') && null !== $object->getGlobalParcelSetting()) {
            $data['globalParcelSetting'] = $this->normalizer->normalize($object->getGlobalParcelSetting(), 'json', $context);
        }
        if ($object->isInitialized('categorySetting') && null !== $object->getCategorySetting()) {
            $values_1 = array();
            foreach ($object->getCategorySetting() as $value_1) {
                $values_1[] = $this->normalizer->normalize($value_1, 'json', $context);
            }
            $data['categorySetting'] = $values_1;
        }
        if ($object->isInitialized('productsSetting') && null !== $object->getProductsSetting()) {
            $values_2 = array();
            foreach ($object->getProductsSetting() as $value_2) {
                $values_2[] = $this->normalizer->normalize($value_2, 'json', $context);
            }
            $data['productsSetting'] = $values_2;
        }
        if ($object->isInitialized('orders') && null !== $object->getOrders()) {
            $values_3 = array();
            foreach ($object->getOrders() as $value_3) {
                $values_3[] = $value_3;
            }
            $data['orders'] = $values_3;
        }
        if ($object->isInitialized('errors') && null !== $object->getErrors()) {
            $values_4 = array();
            foreach ($object->getErrors() as $value_4) {
                $values_4[] = $this->normalizer->normalize($value_4, 'json', $context);
            }
            $data['errors'] = $values_4;
        }
        foreach ($object as $key => $value_5) {
            if (preg_match('/.*/', (string) $key)) {
                $data[$key] = $value_5;
            }
        }
        return $data;
    }
    public function getSupportedTypes(?string $format = null) : ?array
    {
        return array('PPLCZ\\Model\\Model\\ErrorLogModel' => false);
    }
}