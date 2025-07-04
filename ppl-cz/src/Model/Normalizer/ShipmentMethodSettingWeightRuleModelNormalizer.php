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
class ShipmentMethodSettingWeightRuleModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\ShipmentMethodSettingWeightRuleModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\ShipmentMethodSettingWeightRuleModel';
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
        $object = new \PPLCZ\Model\Model\ShipmentMethodSettingWeightRuleModel();
        if (\array_key_exists('from', $data) && \is_int($data['from'])) {
            $data['from'] = (double) $data['from'];
        }
        if (\array_key_exists('to', $data) && \is_int($data['to'])) {
            $data['to'] = (double) $data['to'];
        }
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('from', $data) && $data['from'] !== null) {
            $object->setFrom($data['from']);
            unset($data['from']);
        }
        elseif (\array_key_exists('from', $data) && $data['from'] === null) {
            $object->setFrom(null);
        }
        if (\array_key_exists('to', $data) && $data['to'] !== null) {
            $object->setTo($data['to']);
            unset($data['to']);
        }
        elseif (\array_key_exists('to', $data) && $data['to'] === null) {
            $object->setTo(null);
        }
        if (\array_key_exists('disabledParcelBox', $data) && $data['disabledParcelBox'] !== null) {
            $object->setDisabledParcelBox($data['disabledParcelBox']);
            unset($data['disabledParcelBox']);
        }
        elseif (\array_key_exists('disabledParcelBox', $data) && $data['disabledParcelBox'] === null) {
            $object->setDisabledParcelBox(null);
        }
        if (\array_key_exists('disabledAlzaBox', $data)) {
            $object->setDisabledAlzaBox($data['disabledAlzaBox']);
            unset($data['disabledAlzaBox']);
        }
        if (\array_key_exists('disabledParcelShop', $data) && $data['disabledParcelShop'] !== null) {
            $object->setDisabledParcelShop($data['disabledParcelShop']);
            unset($data['disabledParcelShop']);
        }
        elseif (\array_key_exists('disabledParcelShop', $data) && $data['disabledParcelShop'] === null) {
            $object->setDisabledParcelShop(null);
        }
        if (\array_key_exists('prices', $data)) {
            $values = array();
            foreach ($data['prices'] as $value) {
                $values[] = $this->denormalizer->denormalize($value, 'PPLCZ\\Model\\Model\\ShipmentMethodSettingPriceRuleModel', 'json', $context);
            }
            $object->setPrices($values);
            unset($data['prices']);
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
        if ($object->isInitialized('from') && null !== $object->getFrom()) {
            $data['from'] = $object->getFrom();
        }
        if ($object->isInitialized('to') && null !== $object->getTo()) {
            $data['to'] = $object->getTo();
        }
        if ($object->isInitialized('disabledParcelBox') && null !== $object->getDisabledParcelBox()) {
            $data['disabledParcelBox'] = $object->getDisabledParcelBox();
        }
        if ($object->isInitialized('disabledAlzaBox') && null !== $object->getDisabledAlzaBox()) {
            $data['disabledAlzaBox'] = $object->getDisabledAlzaBox();
        }
        if ($object->isInitialized('disabledParcelShop') && null !== $object->getDisabledParcelShop()) {
            $data['disabledParcelShop'] = $object->getDisabledParcelShop();
        }
        $values = array();
        foreach ($object->getPrices() as $value) {
            $values[] = $this->normalizer->normalize($value, 'json', $context);
        }
        $data['prices'] = $values;
        foreach ($object as $key => $value_1) {
            if (preg_match('/.*/', (string) $key)) {
                $data[$key] = $value_1;
            }
        }
        return $data;
    }
    public function getSupportedTypes(?string $format = null) : ?array
    {
        return array('PPLCZ\\Model\\Model\\ShipmentMethodSettingWeightRuleModel' => false);
    }
}