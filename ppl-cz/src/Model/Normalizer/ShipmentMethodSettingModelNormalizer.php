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
class ShipmentMethodSettingModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\ShipmentMethodSettingModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\ShipmentMethodSettingModel';
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
        $object = new \PPLCZ\Model\Model\ShipmentMethodSettingModel();
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('code', $data)) {
            $object->setCode($data['code']);
            unset($data['code']);
        }
        if (\array_key_exists('costByWeight', $data) && $data['costByWeight'] !== null) {
            $object->setCostByWeight($data['costByWeight']);
            unset($data['costByWeight']);
        }
        elseif (\array_key_exists('costByWeight', $data) && $data['costByWeight'] === null) {
            $object->setCostByWeight(null);
        }
        if (\array_key_exists('parcelBoxes', $data) && $data['parcelBoxes'] !== null) {
            $object->setParcelBoxes($data['parcelBoxes']);
            unset($data['parcelBoxes']);
        }
        elseif (\array_key_exists('parcelBoxes', $data) && $data['parcelBoxes'] === null) {
            $object->setParcelBoxes(null);
        }
        if (\array_key_exists('title', $data) && $data['title'] !== null) {
            $object->setTitle($data['title']);
            unset($data['title']);
        }
        elseif (\array_key_exists('title', $data) && $data['title'] === null) {
            $object->setTitle(null);
        }
        if (\array_key_exists('description', $data) && $data['description'] !== null) {
            $object->setDescription($data['description']);
            unset($data['description']);
        }
        elseif (\array_key_exists('description', $data) && $data['description'] === null) {
            $object->setDescription(null);
        }
        if (\array_key_exists('disablePayments', $data)) {
            $values = array();
            foreach ($data['disablePayments'] as $value) {
                $values[] = $value;
            }
            $object->setDisablePayments($values);
            unset($data['disablePayments']);
        }
        if (\array_key_exists('codPayment', $data) && $data['codPayment'] !== null) {
            $object->setCodPayment($data['codPayment']);
            unset($data['codPayment']);
        }
        elseif (\array_key_exists('codPayment', $data) && $data['codPayment'] === null) {
            $object->setCodPayment(null);
        }
        if (\array_key_exists('priceWithDph', $data) && $data['priceWithDph'] !== null) {
            $object->setPriceWithDph($data['priceWithDph']);
            unset($data['priceWithDph']);
        }
        elseif (\array_key_exists('priceWithDph', $data) && $data['priceWithDph'] === null) {
            $object->setPriceWithDph(null);
        }
        if (\array_key_exists('currencies', $data)) {
            $values_1 = array();
            foreach ($data['currencies'] as $value_1) {
                $values_1[] = $this->denormalizer->denormalize($value_1, 'PPLCZ\\Model\\Model\\ShipmentMethodSettingCurrencyModel', 'json', $context);
            }
            $object->setCurrencies($values_1);
            unset($data['currencies']);
        }
        if (\array_key_exists('weights', $data)) {
            $values_2 = array();
            foreach ($data['weights'] as $value_2) {
                $values_2[] = $this->denormalizer->denormalize($value_2, 'PPLCZ\\Model\\Model\\ShipmentMethodSettingWeightRuleModel', 'json', $context);
            }
            $object->setWeights($values_2);
            unset($data['weights']);
        }
        if (\array_key_exists('disabledParcelBox', $data) && $data['disabledParcelBox'] !== null) {
            $object->setDisabledParcelBox($data['disabledParcelBox']);
            unset($data['disabledParcelBox']);
        }
        elseif (\array_key_exists('disabledParcelBox', $data) && $data['disabledParcelBox'] === null) {
            $object->setDisabledParcelBox(null);
        }
        if (\array_key_exists('disabledAlzaBox', $data) && $data['disabledAlzaBox'] !== null) {
            $object->setDisabledAlzaBox($data['disabledAlzaBox']);
            unset($data['disabledAlzaBox']);
        }
        elseif (\array_key_exists('disabledAlzaBox', $data) && $data['disabledAlzaBox'] === null) {
            $object->setDisabledAlzaBox(null);
        }
        if (\array_key_exists('disabledParcelShop', $data) && $data['disabledParcelShop'] !== null) {
            $object->setDisabledParcelShop($data['disabledParcelShop']);
            unset($data['disabledParcelShop']);
        }
        elseif (\array_key_exists('disabledParcelShop', $data) && $data['disabledParcelShop'] === null) {
            $object->setDisabledParcelShop(null);
        }
        foreach ($data as $key => $value_3) {
            if (preg_match('/.*/', (string) $key)) {
                $object[$key] = $value_3;
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
        if ($object->isInitialized('costByWeight') && null !== $object->getCostByWeight()) {
            $data['costByWeight'] = $object->getCostByWeight();
        }
        if ($object->isInitialized('parcelBoxes') && null !== $object->getParcelBoxes()) {
            $data['parcelBoxes'] = $object->getParcelBoxes();
        }
        if ($object->isInitialized('title') && null !== $object->getTitle()) {
            $data['title'] = $object->getTitle();
        }
        if ($object->isInitialized('description') && null !== $object->getDescription()) {
            $data['description'] = $object->getDescription();
        }
        if ($object->isInitialized('disablePayments') && null !== $object->getDisablePayments()) {
            $values = array();
            foreach ($object->getDisablePayments() as $value) {
                $values[] = $value;
            }
            $data['disablePayments'] = $values;
        }
        if ($object->isInitialized('codPayment') && null !== $object->getCodPayment()) {
            $data['codPayment'] = $object->getCodPayment();
        }
        if ($object->isInitialized('priceWithDph') && null !== $object->getPriceWithDph()) {
            $data['priceWithDph'] = $object->getPriceWithDph();
        }
        $values_1 = array();
        foreach ($object->getCurrencies() as $value_1) {
            $values_1[] = $this->normalizer->normalize($value_1, 'json', $context);
        }
        $data['currencies'] = $values_1;
        $values_2 = array();
        foreach ($object->getWeights() as $value_2) {
            $values_2[] = $this->normalizer->normalize($value_2, 'json', $context);
        }
        $data['weights'] = $values_2;
        if ($object->isInitialized('disabledParcelBox') && null !== $object->getDisabledParcelBox()) {
            $data['disabledParcelBox'] = $object->getDisabledParcelBox();
        }
        if ($object->isInitialized('disabledAlzaBox') && null !== $object->getDisabledAlzaBox()) {
            $data['disabledAlzaBox'] = $object->getDisabledAlzaBox();
        }
        if ($object->isInitialized('disabledParcelShop') && null !== $object->getDisabledParcelShop()) {
            $data['disabledParcelShop'] = $object->getDisabledParcelShop();
        }
        foreach ($object as $key => $value_3) {
            if (preg_match('/.*/', (string) $key)) {
                $data[$key] = $value_3;
            }
        }
        return $data;
    }
    public function getSupportedTypes(?string $format = null) : ?array
    {
        return array('PPLCZ\\Model\\Model\\ShipmentMethodSettingModel' => false);
    }
}