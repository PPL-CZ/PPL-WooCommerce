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
class CartModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\CartModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\CartModel';
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
        $object = new \PPLCZ\Model\Model\CartModel();
        if (\array_key_exists('codFee', $data) && \is_int($data['codFee'])) {
            $data['codFee'] = (double) $data['codFee'];
        }
        if (\array_key_exists('cost', $data) && \is_int($data['cost'])) {
            $data['cost'] = (double) $data['cost'];
        }
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('isPriceWithDph', $data) && $data['isPriceWithDph'] !== null) {
            $object->setIsPriceWithDph($data['isPriceWithDph']);
            unset($data['isPriceWithDph']);
        }
        elseif (\array_key_exists('isPriceWithDph', $data) && $data['isPriceWithDph'] === null) {
            $object->setIsPriceWithDph(null);
        }
        if (\array_key_exists('parcelRequired', $data) && $data['parcelRequired'] !== null) {
            $object->setParcelRequired($data['parcelRequired']);
            unset($data['parcelRequired']);
        }
        elseif (\array_key_exists('parcelRequired', $data) && $data['parcelRequired'] === null) {
            $object->setParcelRequired(null);
        }
        if (\array_key_exists('parcelBoxEnabled', $data) && $data['parcelBoxEnabled'] !== null) {
            $object->setParcelBoxEnabled($data['parcelBoxEnabled']);
            unset($data['parcelBoxEnabled']);
        }
        elseif (\array_key_exists('parcelBoxEnabled', $data) && $data['parcelBoxEnabled'] === null) {
            $object->setParcelBoxEnabled(null);
        }
        if (\array_key_exists('parcelShopEnabled', $data) && $data['parcelShopEnabled'] !== null) {
            $object->setParcelShopEnabled($data['parcelShopEnabled']);
            unset($data['parcelShopEnabled']);
        }
        elseif (\array_key_exists('parcelShopEnabled', $data) && $data['parcelShopEnabled'] === null) {
            $object->setParcelShopEnabled(null);
        }
        if (\array_key_exists('alzaBoxEnabled', $data) && $data['alzaBoxEnabled'] !== null) {
            $object->setAlzaBoxEnabled($data['alzaBoxEnabled']);
            unset($data['alzaBoxEnabled']);
        }
        elseif (\array_key_exists('alzaBoxEnabled', $data) && $data['alzaBoxEnabled'] === null) {
            $object->setAlzaBoxEnabled(null);
        }
        if (\array_key_exists('mapEnabled', $data) && $data['mapEnabled'] !== null) {
            $object->setMapEnabled($data['mapEnabled']);
            unset($data['mapEnabled']);
        }
        elseif (\array_key_exists('mapEnabled', $data) && $data['mapEnabled'] === null) {
            $object->setMapEnabled(null);
        }
        if (\array_key_exists('disabledByWeight', $data) && $data['disabledByWeight'] !== null) {
            $object->setDisabledByWeight($data['disabledByWeight']);
            unset($data['disabledByWeight']);
        }
        elseif (\array_key_exists('disabledByWeight', $data) && $data['disabledByWeight'] === null) {
            $object->setDisabledByWeight(null);
        }
        if (\array_key_exists('disabledByRules', $data) && $data['disabledByRules'] !== null) {
            $object->setDisabledByRules($data['disabledByRules']);
            unset($data['disabledByRules']);
        }
        elseif (\array_key_exists('disabledByRules', $data) && $data['disabledByRules'] === null) {
            $object->setDisabledByRules(null);
        }
        if (\array_key_exists('disabledByCountry', $data)) {
            $object->setDisabledByCountry($data['disabledByCountry']);
            unset($data['disabledByCountry']);
        }
        if (\array_key_exists('enabledParcelCountries', $data) && $data['enabledParcelCountries'] !== null) {
            $values = array();
            foreach ($data['enabledParcelCountries'] as $value) {
                $values[] = $value;
            }
            $object->setEnabledParcelCountries($values);
            unset($data['enabledParcelCountries']);
        }
        elseif (\array_key_exists('enabledParcelCountries', $data) && $data['enabledParcelCountries'] === null) {
            $object->setEnabledParcelCountries(null);
        }
        if (\array_key_exists('ageRequired', $data) && $data['ageRequired'] !== null) {
            $object->setAgeRequired($data['ageRequired']);
            unset($data['ageRequired']);
        }
        elseif (\array_key_exists('ageRequired', $data) && $data['ageRequired'] === null) {
            $object->setAgeRequired(null);
        }
        if (\array_key_exists('codPayment', $data) && $data['codPayment'] !== null) {
            $object->setCodPayment($data['codPayment']);
            unset($data['codPayment']);
        }
        elseif (\array_key_exists('codPayment', $data) && $data['codPayment'] === null) {
            $object->setCodPayment(null);
        }
        if (\array_key_exists('serviceCode', $data)) {
            $object->setServiceCode($data['serviceCode']);
            unset($data['serviceCode']);
        }
        if (\array_key_exists('disablePayments', $data) && $data['disablePayments'] !== null) {
            $values_1 = array();
            foreach ($data['disablePayments'] as $value_1) {
                $values_1[] = $value_1;
            }
            $object->setDisablePayments($values_1);
            unset($data['disablePayments']);
        }
        elseif (\array_key_exists('disablePayments', $data) && $data['disablePayments'] === null) {
            $object->setDisablePayments(null);
        }
        if (\array_key_exists('disabledByProduct', $data)) {
            $object->setDisabledByProduct($data['disabledByProduct']);
            unset($data['disabledByProduct']);
        }
        if (\array_key_exists('disableCod', $data) && $data['disableCod'] !== null) {
            $object->setDisableCod($data['disableCod']);
            unset($data['disableCod']);
        }
        elseif (\array_key_exists('disableCod', $data) && $data['disableCod'] === null) {
            $object->setDisableCod(null);
        }
        if (\array_key_exists('codFee', $data) && $data['codFee'] !== null) {
            $object->setCodFee($data['codFee']);
            unset($data['codFee']);
        }
        elseif (\array_key_exists('codFee', $data) && $data['codFee'] === null) {
            $object->setCodFee(null);
        }
        if (\array_key_exists('codFeeDPH', $data)) {
            $object->setCodFeeDPH($this->denormalizer->denormalize($data['codFeeDPH'], 'PPLCZ\\Model\\Model\\CalculatedDPH', 'json', $context));
            unset($data['codFeeDPH']);
        }
        if (\array_key_exists('cost', $data) && $data['cost'] !== null) {
            $object->setCost($data['cost']);
            unset($data['cost']);
        }
        elseif (\array_key_exists('cost', $data) && $data['cost'] === null) {
            $object->setCost(null);
        }
        if (\array_key_exists('costDPH', $data)) {
            $object->setCostDPH($this->denormalizer->denormalize($data['costDPH'], 'PPLCZ\\Model\\Model\\CalculatedDPH', 'json', $context));
            unset($data['costDPH']);
        }
        if (\array_key_exists('taxableName', $data) && $data['taxableName'] !== null) {
            $object->setTaxableName($data['taxableName']);
            unset($data['taxableName']);
        }
        elseif (\array_key_exists('taxableName', $data) && $data['taxableName'] === null) {
            $object->setTaxableName(null);
        }
        foreach ($data as $key => $value_2) {
            if (preg_match('/.*/', (string) $key)) {
                $object[$key] = $value_2;
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
        if ($object->isInitialized('isPriceWithDph') && null !== $object->getIsPriceWithDph()) {
            $data['isPriceWithDph'] = $object->getIsPriceWithDph();
        }
        if ($object->isInitialized('parcelRequired') && null !== $object->getParcelRequired()) {
            $data['parcelRequired'] = $object->getParcelRequired();
        }
        if ($object->isInitialized('parcelBoxEnabled') && null !== $object->getParcelBoxEnabled()) {
            $data['parcelBoxEnabled'] = $object->getParcelBoxEnabled();
        }
        if ($object->isInitialized('parcelShopEnabled') && null !== $object->getParcelShopEnabled()) {
            $data['parcelShopEnabled'] = $object->getParcelShopEnabled();
        }
        if ($object->isInitialized('alzaBoxEnabled') && null !== $object->getAlzaBoxEnabled()) {
            $data['alzaBoxEnabled'] = $object->getAlzaBoxEnabled();
        }
        if ($object->isInitialized('mapEnabled') && null !== $object->getMapEnabled()) {
            $data['mapEnabled'] = $object->getMapEnabled();
        }
        if ($object->isInitialized('disabledByWeight') && null !== $object->getDisabledByWeight()) {
            $data['disabledByWeight'] = $object->getDisabledByWeight();
        }
        if ($object->isInitialized('disabledByRules') && null !== $object->getDisabledByRules()) {
            $data['disabledByRules'] = $object->getDisabledByRules();
        }
        if ($object->isInitialized('disabledByCountry') && null !== $object->getDisabledByCountry()) {
            $data['disabledByCountry'] = $object->getDisabledByCountry();
        }
        if ($object->isInitialized('enabledParcelCountries') && null !== $object->getEnabledParcelCountries()) {
            $values = array();
            foreach ($object->getEnabledParcelCountries() as $value) {
                $values[] = $value;
            }
            $data['enabledParcelCountries'] = $values;
        }
        if ($object->isInitialized('ageRequired') && null !== $object->getAgeRequired()) {
            $data['ageRequired'] = $object->getAgeRequired();
        }
        if ($object->isInitialized('codPayment') && null !== $object->getCodPayment()) {
            $data['codPayment'] = $object->getCodPayment();
        }
        if ($object->isInitialized('serviceCode') && null !== $object->getServiceCode()) {
            $data['serviceCode'] = $object->getServiceCode();
        }
        if ($object->isInitialized('disablePayments') && null !== $object->getDisablePayments()) {
            $values_1 = array();
            foreach ($object->getDisablePayments() as $value_1) {
                $values_1[] = $value_1;
            }
            $data['disablePayments'] = $values_1;
        }
        if ($object->isInitialized('disabledByProduct') && null !== $object->getDisabledByProduct()) {
            $data['disabledByProduct'] = $object->getDisabledByProduct();
        }
        if ($object->isInitialized('disableCod') && null !== $object->getDisableCod()) {
            $data['disableCod'] = $object->getDisableCod();
        }
        if ($object->isInitialized('codFee') && null !== $object->getCodFee()) {
            $data['codFee'] = $object->getCodFee();
        }
        if ($object->isInitialized('codFeeDPH') && null !== $object->getCodFeeDPH()) {
            $data['codFeeDPH'] = $this->normalizer->normalize($object->getCodFeeDPH(), 'json', $context);
        }
        if ($object->isInitialized('cost') && null !== $object->getCost()) {
            $data['cost'] = $object->getCost();
        }
        if ($object->isInitialized('costDPH') && null !== $object->getCostDPH()) {
            $data['costDPH'] = $this->normalizer->normalize($object->getCostDPH(), 'json', $context);
        }
        if ($object->isInitialized('taxableName') && null !== $object->getTaxableName()) {
            $data['taxableName'] = $object->getTaxableName();
        }
        foreach ($object as $key => $value_2) {
            if (preg_match('/.*/', (string) $key)) {
                $data[$key] = $value_2;
            }
        }
        return $data;
    }
    public function getSupportedTypes(?string $format = null) : ?array
    {
        return array('PPLCZ\\Model\\Model\\CartModel' => false);
    }
}