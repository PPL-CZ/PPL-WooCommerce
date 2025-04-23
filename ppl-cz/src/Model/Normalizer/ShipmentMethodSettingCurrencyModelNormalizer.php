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
class ShipmentMethodSettingCurrencyModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\ShipmentMethodSettingCurrencyModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\ShipmentMethodSettingCurrencyModel';
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
        $object = new \PPLCZ\Model\Model\ShipmentMethodSettingCurrencyModel();
        if (\array_key_exists('costOrderFree', $data) && \is_int($data['costOrderFree'])) {
            $data['costOrderFree'] = (double) $data['costOrderFree'];
        }
        if (\array_key_exists('costCodFee', $data) && \is_int($data['costCodFee'])) {
            $data['costCodFee'] = (double) $data['costCodFee'];
        }
        if (\array_key_exists('costOrderFreeCod', $data) && \is_int($data['costOrderFreeCod'])) {
            $data['costOrderFreeCod'] = (double) $data['costOrderFreeCod'];
        }
        if (\array_key_exists('cost', $data) && \is_int($data['cost'])) {
            $data['cost'] = (double) $data['cost'];
        }
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('enabled', $data) && $data['enabled'] !== null) {
            $object->setEnabled($data['enabled']);
            unset($data['enabled']);
        }
        elseif (\array_key_exists('enabled', $data) && $data['enabled'] === null) {
            $object->setEnabled(null);
        }
        if (\array_key_exists('currency', $data)) {
            $object->setCurrency($data['currency']);
            unset($data['currency']);
        }
        if (\array_key_exists('costOrderFree', $data) && $data['costOrderFree'] !== null) {
            $object->setCostOrderFree($data['costOrderFree']);
            unset($data['costOrderFree']);
        }
        elseif (\array_key_exists('costOrderFree', $data) && $data['costOrderFree'] === null) {
            $object->setCostOrderFree(null);
        }
        if (\array_key_exists('costCodFee', $data) && $data['costCodFee'] !== null) {
            $object->setCostCodFee($data['costCodFee']);
            unset($data['costCodFee']);
        }
        elseif (\array_key_exists('costCodFee', $data) && $data['costCodFee'] === null) {
            $object->setCostCodFee(null);
        }
        if (\array_key_exists('costCodFeeAlways', $data) && $data['costCodFeeAlways'] !== null) {
            $object->setCostCodFeeAlways($data['costCodFeeAlways']);
            unset($data['costCodFeeAlways']);
        }
        elseif (\array_key_exists('costCodFeeAlways', $data) && $data['costCodFeeAlways'] === null) {
            $object->setCostCodFeeAlways(null);
        }
        if (\array_key_exists('costOrderFreeCod', $data) && $data['costOrderFreeCod'] !== null) {
            $object->setCostOrderFreeCod($data['costOrderFreeCod']);
            unset($data['costOrderFreeCod']);
        }
        elseif (\array_key_exists('costOrderFreeCod', $data) && $data['costOrderFreeCod'] === null) {
            $object->setCostOrderFreeCod(null);
        }
        if (\array_key_exists('cost', $data) && $data['cost'] !== null) {
            $object->setCost($data['cost']);
            unset($data['cost']);
        }
        elseif (\array_key_exists('cost', $data) && $data['cost'] === null) {
            $object->setCost(null);
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
        if ($object->isInitialized('enabled') && null !== $object->getEnabled()) {
            $data['enabled'] = $object->getEnabled();
        }
        $data['currency'] = $object->getCurrency();
        if ($object->isInitialized('costOrderFree') && null !== $object->getCostOrderFree()) {
            $data['costOrderFree'] = $object->getCostOrderFree();
        }
        if ($object->isInitialized('costCodFee') && null !== $object->getCostCodFee()) {
            $data['costCodFee'] = $object->getCostCodFee();
        }
        if ($object->isInitialized('costCodFeeAlways') && null !== $object->getCostCodFeeAlways()) {
            $data['costCodFeeAlways'] = $object->getCostCodFeeAlways();
        }
        if ($object->isInitialized('costOrderFreeCod') && null !== $object->getCostOrderFreeCod()) {
            $data['costOrderFreeCod'] = $object->getCostOrderFreeCod();
        }
        if ($object->isInitialized('cost') && null !== $object->getCost()) {
            $data['cost'] = $object->getCost();
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
        return array('PPLCZ\\Model\\Model\\ShipmentMethodSettingCurrencyModel' => false);
    }
}