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
class CategoryModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\CategoryModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\CategoryModel';
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
        $object = new \PPLCZ\Model\Model\CategoryModel();
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('pplConfirmAge15', $data) && $data['pplConfirmAge15'] !== null) {
            $object->setPplConfirmAge15($data['pplConfirmAge15']);
            unset($data['pplConfirmAge15']);
        }
        elseif (\array_key_exists('pplConfirmAge15', $data) && $data['pplConfirmAge15'] === null) {
            $object->setPplConfirmAge15(null);
        }
        if (\array_key_exists('pplConfirmAge18', $data) && $data['pplConfirmAge18'] !== null) {
            $object->setPplConfirmAge18($data['pplConfirmAge18']);
            unset($data['pplConfirmAge18']);
        }
        elseif (\array_key_exists('pplConfirmAge18', $data) && $data['pplConfirmAge18'] === null) {
            $object->setPplConfirmAge18(null);
        }
        if (\array_key_exists('pplDisabledParcelBox', $data) && $data['pplDisabledParcelBox'] !== null) {
            $object->setPplDisabledParcelBox($data['pplDisabledParcelBox']);
            unset($data['pplDisabledParcelBox']);
        }
        elseif (\array_key_exists('pplDisabledParcelBox', $data) && $data['pplDisabledParcelBox'] === null) {
            $object->setPplDisabledParcelBox(null);
        }
        if (\array_key_exists('pplDisabledAlzaBox', $data)) {
            $object->setPplDisabledAlzaBox($data['pplDisabledAlzaBox']);
            unset($data['pplDisabledAlzaBox']);
        }
        if (\array_key_exists('pplDisabledParcelShop', $data) && $data['pplDisabledParcelShop'] !== null) {
            $object->setPplDisabledParcelShop($data['pplDisabledParcelShop']);
            unset($data['pplDisabledParcelShop']);
        }
        elseif (\array_key_exists('pplDisabledParcelShop', $data) && $data['pplDisabledParcelShop'] === null) {
            $object->setPplDisabledParcelShop(null);
        }
        if (\array_key_exists('pplDisabledTransport', $data) && $data['pplDisabledTransport'] !== null) {
            $values = array();
            foreach ($data['pplDisabledTransport'] as $value) {
                $values[] = $value;
            }
            $object->setPplDisabledTransport($values);
            unset($data['pplDisabledTransport']);
        }
        elseif (\array_key_exists('pplDisabledTransport', $data) && $data['pplDisabledTransport'] === null) {
            $object->setPplDisabledTransport(null);
        }
        if (\array_key_exists('pplSize', $data)) {
            $object->setPplSize($this->denormalizer->denormalize($data['pplSize'], 'PPLCZ\\Model\\Model\\PackageSizeModel', 'json', $context));
            unset($data['pplSize']);
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
        if ($object->isInitialized('pplConfirmAge15') && null !== $object->getPplConfirmAge15()) {
            $data['pplConfirmAge15'] = $object->getPplConfirmAge15();
        }
        if ($object->isInitialized('pplConfirmAge18') && null !== $object->getPplConfirmAge18()) {
            $data['pplConfirmAge18'] = $object->getPplConfirmAge18();
        }
        if ($object->isInitialized('pplDisabledParcelBox') && null !== $object->getPplDisabledParcelBox()) {
            $data['pplDisabledParcelBox'] = $object->getPplDisabledParcelBox();
        }
        if ($object->isInitialized('pplDisabledAlzaBox') && null !== $object->getPplDisabledAlzaBox()) {
            $data['pplDisabledAlzaBox'] = $object->getPplDisabledAlzaBox();
        }
        if ($object->isInitialized('pplDisabledParcelShop') && null !== $object->getPplDisabledParcelShop()) {
            $data['pplDisabledParcelShop'] = $object->getPplDisabledParcelShop();
        }
        if ($object->isInitialized('pplDisabledTransport') && null !== $object->getPplDisabledTransport()) {
            $values = array();
            foreach ($object->getPplDisabledTransport() as $value) {
                $values[] = $value;
            }
            $data['pplDisabledTransport'] = $values;
        }
        if ($object->isInitialized('pplSize') && null !== $object->getPplSize()) {
            $data['pplSize'] = $this->normalizer->normalize($object->getPplSize(), 'json', $context);
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
        return array('PPLCZ\\Model\\Model\\CategoryModel' => false);
    }
}