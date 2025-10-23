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
class ShipmentModelNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return $type === 'PPLCZ\\Model\\Model\\ShipmentModel';
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && get_class($data) === 'PPLCZ\\Model\\Model\\ShipmentModel';
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
        $object = new \PPLCZ\Model\Model\ShipmentModel();
        if (\array_key_exists('id', $data) && \is_int($data['id'])) {
            $data['id'] = (double) $data['id'];
        }
        if (\array_key_exists('codValue', $data) && \is_int($data['codValue'])) {
            $data['codValue'] = (double) $data['codValue'];
        }
        if (\array_key_exists('orderId', $data) && \is_int($data['orderId'])) {
            $data['orderId'] = (double) $data['orderId'];
        }
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('id', $data) && $data['id'] !== null) {
            $object->setId($data['id']);
            unset($data['id']);
        }
        elseif (\array_key_exists('id', $data) && $data['id'] === null) {
            $object->setId(null);
        }
        if (\array_key_exists('packages', $data) && $data['packages'] !== null) {
            $values = array();
            foreach ($data['packages'] as $value) {
                $values[] = $this->denormalizer->denormalize($value, 'PPLCZ\\Model\\Model\\PackageModel', 'json', $context);
            }
            $object->setPackages($values);
            unset($data['packages']);
        }
        elseif (\array_key_exists('packages', $data) && $data['packages'] === null) {
            $object->setPackages(null);
        }
        if (\array_key_exists('printState', $data) && $data['printState'] !== null) {
            $object->setPrintState($data['printState']);
            unset($data['printState']);
        }
        elseif (\array_key_exists('printState', $data) && $data['printState'] === null) {
            $object->setPrintState(null);
        }
        if (\array_key_exists('importState', $data) && $data['importState'] !== null) {
            $object->setImportState($data['importState']);
            unset($data['importState']);
        }
        elseif (\array_key_exists('importState', $data) && $data['importState'] === null) {
            $object->setImportState(null);
        }
        if (\array_key_exists('referenceId', $data) && $data['referenceId'] !== null) {
            $object->setReferenceId($data['referenceId']);
            unset($data['referenceId']);
        }
        elseif (\array_key_exists('referenceId', $data) && $data['referenceId'] === null) {
            $object->setReferenceId(null);
        }
        if (\array_key_exists('note', $data) && $data['note'] !== null) {
            $object->setNote($data['note']);
            unset($data['note']);
        }
        elseif (\array_key_exists('note', $data) && $data['note'] === null) {
            $object->setNote(null);
        }
        if (\array_key_exists('codValue', $data) && $data['codValue'] !== null) {
            $object->setCodValue($data['codValue']);
            unset($data['codValue']);
        }
        elseif (\array_key_exists('codValue', $data) && $data['codValue'] === null) {
            $object->setCodValue(null);
        }
        if (\array_key_exists('codValueCurrency', $data) && $data['codValueCurrency'] !== null) {
            $object->setCodValueCurrency($data['codValueCurrency']);
            unset($data['codValueCurrency']);
        }
        elseif (\array_key_exists('codValueCurrency', $data) && $data['codValueCurrency'] === null) {
            $object->setCodValueCurrency(null);
        }
        if (\array_key_exists('codVariableNumber', $data) && $data['codVariableNumber'] !== null) {
            $object->setCodVariableNumber($data['codVariableNumber']);
            unset($data['codVariableNumber']);
        }
        elseif (\array_key_exists('codVariableNumber', $data) && $data['codVariableNumber'] === null) {
            $object->setCodVariableNumber(null);
        }
        if (\array_key_exists('serviceCode', $data) && $data['serviceCode'] !== null) {
            $object->setServiceCode($data['serviceCode']);
            unset($data['serviceCode']);
        }
        elseif (\array_key_exists('serviceCode', $data) && $data['serviceCode'] === null) {
            $object->setServiceCode(null);
        }
        if (\array_key_exists('serviceName', $data) && $data['serviceName'] !== null) {
            $object->setServiceName($data['serviceName']);
            unset($data['serviceName']);
        }
        elseif (\array_key_exists('serviceName', $data) && $data['serviceName'] === null) {
            $object->setServiceName(null);
        }
        if (\array_key_exists('batchId', $data) && $data['batchId'] !== null) {
            $object->setBatchId($data['batchId']);
            unset($data['batchId']);
        }
        elseif (\array_key_exists('batchId', $data) && $data['batchId'] === null) {
            $object->setBatchId(null);
        }
        if (\array_key_exists('batchRemoteId', $data) && $data['batchRemoteId'] !== null) {
            $object->setBatchRemoteId($data['batchRemoteId']);
            unset($data['batchRemoteId']);
        }
        elseif (\array_key_exists('batchRemoteId', $data) && $data['batchRemoteId'] === null) {
            $object->setBatchRemoteId(null);
        }
        if (\array_key_exists('hasParcel', $data) && $data['hasParcel'] !== null) {
            $object->setHasParcel($data['hasParcel']);
            unset($data['hasParcel']);
        }
        elseif (\array_key_exists('hasParcel', $data) && $data['hasParcel'] === null) {
            $object->setHasParcel(null);
        }
        if (\array_key_exists('orderId', $data) && $data['orderId'] !== null) {
            $object->setOrderId($data['orderId']);
            unset($data['orderId']);
        }
        elseif (\array_key_exists('orderId', $data) && $data['orderId'] === null) {
            $object->setOrderId(null);
        }
        if (\array_key_exists('parcel', $data)) {
            $object->setParcel($this->denormalizer->denormalize($data['parcel'], 'PPLCZ\\Model\\Model\\ParcelAddressModel', 'json', $context));
            unset($data['parcel']);
        }
        if (\array_key_exists('recipient', $data)) {
            $object->setRecipient($this->denormalizer->denormalize($data['recipient'], 'PPLCZ\\Model\\Model\\RecipientAddressModel', 'json', $context));
            unset($data['recipient']);
        }
        if (\array_key_exists('sender', $data)) {
            $object->setSender($this->denormalizer->denormalize($data['sender'], 'PPLCZ\\Model\\Model\\SenderAddressModel', 'json', $context));
            unset($data['sender']);
        }
        if (\array_key_exists('age', $data) && $data['age'] !== null) {
            $object->setAge($data['age']);
            unset($data['age']);
        }
        elseif (\array_key_exists('age', $data) && $data['age'] === null) {
            $object->setAge(null);
        }
        if (\array_key_exists('lock', $data) && $data['lock'] !== null) {
            $object->setLock($data['lock']);
            unset($data['lock']);
        }
        elseif (\array_key_exists('lock', $data) && $data['lock'] === null) {
            $object->setLock(null);
        }
        if (\array_key_exists('importErrors', $data) && $data['importErrors'] !== null) {
            $values_1 = array();
            foreach ($data['importErrors'] as $value_1) {
                $values_1[] = $value_1;
            }
            $object->setImportErrors($values_1);
            unset($data['importErrors']);
        }
        elseif (\array_key_exists('importErrors', $data) && $data['importErrors'] === null) {
            $object->setImportErrors(null);
        }
        if (\array_key_exists('phase', $data) && $data['phase'] !== null) {
            $object->setPhase($data['phase']);
            unset($data['phase']);
        }
        elseif (\array_key_exists('phase', $data) && $data['phase'] === null) {
            $object->setPhase(null);
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
        if ($object->isInitialized('id') && null !== $object->getId()) {
            $data['id'] = $object->getId();
        }
        if ($object->isInitialized('packages') && null !== $object->getPackages()) {
            $values = array();
            foreach ($object->getPackages() as $value) {
                $values[] = $this->normalizer->normalize($value, 'json', $context);
            }
            $data['packages'] = $values;
        }
        if ($object->isInitialized('printState') && null !== $object->getPrintState()) {
            $data['printState'] = $object->getPrintState();
        }
        if ($object->isInitialized('importState') && null !== $object->getImportState()) {
            $data['importState'] = $object->getImportState();
        }
        if ($object->isInitialized('referenceId') && null !== $object->getReferenceId()) {
            $data['referenceId'] = $object->getReferenceId();
        }
        if ($object->isInitialized('note') && null !== $object->getNote()) {
            $data['note'] = $object->getNote();
        }
        if ($object->isInitialized('codValue') && null !== $object->getCodValue()) {
            $data['codValue'] = $object->getCodValue();
        }
        if ($object->isInitialized('codValueCurrency') && null !== $object->getCodValueCurrency()) {
            $data['codValueCurrency'] = $object->getCodValueCurrency();
        }
        if ($object->isInitialized('codVariableNumber') && null !== $object->getCodVariableNumber()) {
            $data['codVariableNumber'] = $object->getCodVariableNumber();
        }
        if ($object->isInitialized('serviceCode') && null !== $object->getServiceCode()) {
            $data['serviceCode'] = $object->getServiceCode();
        }
        if ($object->isInitialized('serviceName') && null !== $object->getServiceName()) {
            $data['serviceName'] = $object->getServiceName();
        }
        if ($object->isInitialized('batchId') && null !== $object->getBatchId()) {
            $data['batchId'] = $object->getBatchId();
        }
        if ($object->isInitialized('batchRemoteId') && null !== $object->getBatchRemoteId()) {
            $data['batchRemoteId'] = $object->getBatchRemoteId();
        }
        if ($object->isInitialized('hasParcel') && null !== $object->getHasParcel()) {
            $data['hasParcel'] = $object->getHasParcel();
        }
        if ($object->isInitialized('orderId') && null !== $object->getOrderId()) {
            $data['orderId'] = $object->getOrderId();
        }
        if ($object->isInitialized('parcel') && null !== $object->getParcel()) {
            $data['parcel'] = $this->normalizer->normalize($object->getParcel(), 'json', $context);
        }
        if ($object->isInitialized('recipient') && null !== $object->getRecipient()) {
            $data['recipient'] = $this->normalizer->normalize($object->getRecipient(), 'json', $context);
        }
        if ($object->isInitialized('sender') && null !== $object->getSender()) {
            $data['sender'] = $this->normalizer->normalize($object->getSender(), 'json', $context);
        }
        if ($object->isInitialized('age') && null !== $object->getAge()) {
            $data['age'] = $object->getAge();
        }
        if ($object->isInitialized('lock') && null !== $object->getLock()) {
            $data['lock'] = $object->getLock();
        }
        if ($object->isInitialized('importErrors') && null !== $object->getImportErrors()) {
            $values_1 = array();
            foreach ($object->getImportErrors() as $value_1) {
                $values_1[] = $value_1;
            }
            $data['importErrors'] = $values_1;
        }
        if ($object->isInitialized('phase') && null !== $object->getPhase()) {
            $data['phase'] = $object->getPhase();
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
        return array('PPLCZ\\Model\\Model\\ShipmentModel' => false);
    }
}