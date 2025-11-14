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
class ParcelPlacesModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\ParcelPlacesModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\ParcelPlacesModel';
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
        $object = new \PPLCZ\Model\Model\ParcelPlacesModel();
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('disabledByStripe', $data) && $data['disabledByStripe'] !== null) {
            $object->setDisabledByStripe($data['disabledByStripe']);
            unset($data['disabledByStripe']);
        }
        elseif (\array_key_exists('disabledByStripe', $data) && $data['disabledByStripe'] === null) {
            $object->setDisabledByStripe(null);
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
        if (\array_key_exists('disabledCountries', $data) && $data['disabledCountries'] !== null) {
            $values = array();
            foreach ($data['disabledCountries'] as $value) {
                $values[] = $value;
            }
            $object->setDisabledCountries($values);
            unset($data['disabledCountries']);
        }
        elseif (\array_key_exists('disabledCountries', $data) && $data['disabledCountries'] === null) {
            $object->setDisabledCountries(null);
        }
        if (\array_key_exists('mapLanguage', $data) && $data['mapLanguage'] !== null) {
            $object->setMapLanguage($data['mapLanguage']);
            unset($data['mapLanguage']);
        }
        elseif (\array_key_exists('mapLanguage', $data) && $data['mapLanguage'] === null) {
            $object->setMapLanguage(null);
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
        if ($object->isInitialized('disabledByStripe') && null !== $object->getDisabledByStripe()) {
            $data['disabledByStripe'] = $object->getDisabledByStripe();
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
        if ($object->isInitialized('disabledCountries') && null !== $object->getDisabledCountries()) {
            $values = array();
            foreach ($object->getDisabledCountries() as $value) {
                $values[] = $value;
            }
            $data['disabledCountries'] = $values;
        }
        if ($object->isInitialized('mapLanguage') && null !== $object->getMapLanguage()) {
            $data['mapLanguage'] = $object->getMapLanguage();
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
        return array('PPLCZ\\Model\\Model\\ParcelPlacesModel' => false);
    }
}