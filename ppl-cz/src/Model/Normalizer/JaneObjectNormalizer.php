<?php

namespace PPLCZ\Model\Normalizer;

use PPLCZ\Model\Runtime\Normalizer\CheckArray;
use PPLCZ\Model\Runtime\Normalizer\ValidatorTrait;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class JaneObjectNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    protected $normalizers = array('PPLCZ\\Model\\Model\\ProductModel' => 'PPLCZ\\Model\\Normalizer\\ProductModelNormalizer', 'PPLCZ\\Model\\Model\\CategoryModel' => 'PPLCZ\\Model\\Normalizer\\CategoryModelNormalizer', 'PPLCZ\\Model\\Model\\ParcelDataModel' => 'PPLCZ\\Model\\Normalizer\\ParcelDataModelNormalizer', 'PPLCZ\\Model\\Model\\ParcelDataModelGps' => 'PPLCZ\\Model\\Normalizer\\ParcelDataModelGpsNormalizer', 'PPLCZ\\Model\\Model\\ParcelPlacesModel' => 'PPLCZ\\Model\\Normalizer\\ParcelPlacesModelNormalizer', 'PPLCZ\\Model\\Model\\MyApi2' => 'PPLCZ\\Model\\Normalizer\\MyApi2Normalizer', 'PPLCZ\\Model\\Model\\WhisperSettingModel' => 'PPLCZ\\Model\\Normalizer\\WhisperSettingModelNormalizer', 'PPLCZ\\Model\\Model\\WhisperAddressModel' => 'PPLCZ\\Model\\Normalizer\\WhisperAddressModelNormalizer', 'PPLCZ\\Model\\Model\\UpdateShipmentModel' => 'PPLCZ\\Model\\Normalizer\\UpdateShipmentModelNormalizer', 'PPLCZ\\Model\\Model\\UpdateParcelModel' => 'PPLCZ\\Model\\Normalizer\\UpdateParcelModelNormalizer', 'PPLCZ\\Model\\Model\\UpdateShipmentSenderModel' => 'PPLCZ\\Model\\Normalizer\\UpdateShipmentSenderModelNormalizer', 'PPLCZ\\Model\\Model\\UpdateShipmentParcelModel' => 'PPLCZ\\Model\\Normalizer\\UpdateShipmentParcelModelNormalizer', 'PPLCZ\\Model\\Model\\CreateShipmentLabelBatchModel' => 'PPLCZ\\Model\\Normalizer\\CreateShipmentLabelBatchModelNormalizer', 'PPLCZ\\Model\\Model\\ShipmentLabelRefreshBatchModel' => 'PPLCZ\\Model\\Normalizer\\ShipmentLabelRefreshBatchModelNormalizer', 'PPLCZ\\Model\\Model\\PrepareShipmentBatchModel' => 'PPLCZ\\Model\\Normalizer\\PrepareShipmentBatchModelNormalizer', 'PPLCZ\\Model\\Model\\PrepareShipmentBatchItemModel' => 'PPLCZ\\Model\\Normalizer\\PrepareShipmentBatchItemModelNormalizer', 'PPLCZ\\Model\\Model\\PrepareShipmentBatchReturnModel' => 'PPLCZ\\Model\\Normalizer\\PrepareShipmentBatchReturnModelNormalizer', 'PPLCZ\\Model\\Model\\RefreshShipmentBatchReturnModel' => 'PPLCZ\\Model\\Normalizer\\RefreshShipmentBatchReturnModelNormalizer', 'PPLCZ\\Model\\Model\\PackageModel' => 'PPLCZ\\Model\\Normalizer\\PackageModelNormalizer', 'PPLCZ\\Model\\Model\\ParcelAddressModel' => 'PPLCZ\\Model\\Normalizer\\ParcelAddressModelNormalizer', 'PPLCZ\\Model\\Model\\LabelPrintModel' => 'PPLCZ\\Model\\Normalizer\\LabelPrintModelNormalizer', 'PPLCZ\\Model\\Model\\CollectionAddressModel' => 'PPLCZ\\Model\\Normalizer\\CollectionAddressModelNormalizer', 'PPLCZ\\Model\\Model\\SenderAddressModel' => 'PPLCZ\\Model\\Normalizer\\SenderAddressModelNormalizer', 'PPLCZ\\Model\\Model\\RecipientAddressModel' => 'PPLCZ\\Model\\Normalizer\\RecipientAddressModelNormalizer', 'PPLCZ\\Model\\Model\\CartModel' => 'PPLCZ\\Model\\Normalizer\\CartModelNormalizer', 'PPLCZ\\Model\\Model\\ShipmentModel' => 'PPLCZ\\Model\\Normalizer\\ShipmentModelNormalizer', 'PPLCZ\\Model\\Model\\CurrencyModel' => 'PPLCZ\\Model\\Normalizer\\CurrencyModelNormalizer', 'PPLCZ\\Model\\Model\\ShipmentPhaseModel' => 'PPLCZ\\Model\\Normalizer\\ShipmentPhaseModelNormalizer', 'PPLCZ\\Model\\Model\\SyncPhasesModel' => 'PPLCZ\\Model\\Normalizer\\SyncPhasesModelNormalizer', 'PPLCZ\\Model\\Model\\UpdateSyncPhasesModel' => 'PPLCZ\\Model\\Normalizer\\UpdateSyncPhasesModelNormalizer', 'PPLCZ\\Model\\Model\\UpdateSyncPhasesModelPhasesItem' => 'PPLCZ\\Model\\Normalizer\\UpdateSyncPhasesModelPhasesItemNormalizer', 'PPLCZ\\Model\\Model\\CountryModel' => 'PPLCZ\\Model\\Normalizer\\CountryModelNormalizer', 'PPLCZ\\Model\\Model\\ShipmentMethodModel' => 'PPLCZ\\Model\\Normalizer\\ShipmentMethodModelNormalizer', 'PPLCZ\\Model\\Model\\NewCollectionModel' => 'PPLCZ\\Model\\Normalizer\\NewCollectionModelNormalizer', 'PPLCZ\\Model\\Model\\CollectionModel' => 'PPLCZ\\Model\\Normalizer\\CollectionModelNormalizer', 'PPLCZ\\Model\\Model\\ShipmentMethodSettingModel' => 'PPLCZ\\Model\\Normalizer\\ShipmentMethodSettingModelNormalizer', 'PPLCZ\\Model\\Model\\ShipmentMethodSettingCurrencyModel' => 'PPLCZ\\Model\\Normalizer\\ShipmentMethodSettingCurrencyModelNormalizer', 'PPLCZ\\Model\\Model\\ShipmentMethodSettingPriceRuleModel' => 'PPLCZ\\Model\\Normalizer\\ShipmentMethodSettingPriceRuleModelNormalizer', 'PPLCZ\\Model\\Model\\ShipmentMethodSettingWeightRuleModel' => 'PPLCZ\\Model\\Normalizer\\ShipmentMethodSettingWeightRuleModelNormalizer', '\\Jane\\Component\\JsonSchemaRuntime\\Reference' => '\\PPLCZ\\Model\\Runtime\\Normalizer\\ReferenceNormalizer'), $normalizersCache = array();
    public function supportsDenormalization($data, $type, $format = null, array $context = array()) : bool
    {
        return array_key_exists($type, $this->normalizers);
    }
    public function supportsNormalization($data, $format = null, array $context = array()) : bool
    {
        return is_object($data) && array_key_exists(get_class($data), $this->normalizers);
    }
    /**
     * @return array|string|int|float|bool|\ArrayObject|null
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $normalizerClass = $this->normalizers[get_class($object)];
        $normalizer = $this->getNormalizer($normalizerClass);
        return $normalizer->normalize($object, $format, $context);
    }
    /**
     * @return mixed
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $denormalizerClass = $this->normalizers[$class];
        $denormalizer = $this->getNormalizer($denormalizerClass);
        return $denormalizer->denormalize($data, $class, $format, $context);
    }
    private function getNormalizer(string $normalizerClass)
    {
        return $this->normalizersCache[$normalizerClass] ?? $this->initNormalizer($normalizerClass);
    }
    private function initNormalizer(string $normalizerClass)
    {
        $normalizer = new $normalizerClass();
        $normalizer->setNormalizer($this->normalizer);
        $normalizer->setDenormalizer($this->denormalizer);
        $this->normalizersCache[$normalizerClass] = $normalizer;
        return $normalizer;
    }
    public function getSupportedTypes(?string $format = null) : ?array
    {
        return array('PPLCZ\\Model\\Model\\ProductModel' => false, 'PPLCZ\\Model\\Model\\CategoryModel' => false, 'PPLCZ\\Model\\Model\\ParcelDataModel' => false, 'PPLCZ\\Model\\Model\\ParcelDataModelGps' => false, 'PPLCZ\\Model\\Model\\ParcelPlacesModel' => false, 'PPLCZ\\Model\\Model\\MyApi2' => false, 'PPLCZ\\Model\\Model\\WhisperSettingModel' => false, 'PPLCZ\\Model\\Model\\WhisperAddressModel' => false, 'PPLCZ\\Model\\Model\\UpdateShipmentModel' => false, 'PPLCZ\\Model\\Model\\UpdateParcelModel' => false, 'PPLCZ\\Model\\Model\\UpdateShipmentSenderModel' => false, 'PPLCZ\\Model\\Model\\UpdateShipmentParcelModel' => false, 'PPLCZ\\Model\\Model\\CreateShipmentLabelBatchModel' => false, 'PPLCZ\\Model\\Model\\ShipmentLabelRefreshBatchModel' => false, 'PPLCZ\\Model\\Model\\PrepareShipmentBatchModel' => false, 'PPLCZ\\Model\\Model\\PrepareShipmentBatchItemModel' => false, 'PPLCZ\\Model\\Model\\PrepareShipmentBatchReturnModel' => false, 'PPLCZ\\Model\\Model\\RefreshShipmentBatchReturnModel' => false, 'PPLCZ\\Model\\Model\\PackageModel' => false, 'PPLCZ\\Model\\Model\\ParcelAddressModel' => false, 'PPLCZ\\Model\\Model\\LabelPrintModel' => false, 'PPLCZ\\Model\\Model\\CollectionAddressModel' => false, 'PPLCZ\\Model\\Model\\SenderAddressModel' => false, 'PPLCZ\\Model\\Model\\RecipientAddressModel' => false, 'PPLCZ\\Model\\Model\\CartModel' => false, 'PPLCZ\\Model\\Model\\ShipmentModel' => false, 'PPLCZ\\Model\\Model\\CurrencyModel' => false, 'PPLCZ\\Model\\Model\\ShipmentPhaseModel' => false, 'PPLCZ\\Model\\Model\\SyncPhasesModel' => false, 'PPLCZ\\Model\\Model\\UpdateSyncPhasesModel' => false, 'PPLCZ\\Model\\Model\\UpdateSyncPhasesModelPhasesItem' => false, 'PPLCZ\\Model\\Model\\CountryModel' => false, 'PPLCZ\\Model\\Model\\ShipmentMethodModel' => false, 'PPLCZ\\Model\\Model\\NewCollectionModel' => false, 'PPLCZ\\Model\\Model\\CollectionModel' => false, 'PPLCZ\\Model\\Model\\ShipmentMethodSettingModel' => false, 'PPLCZ\\Model\\Model\\ShipmentMethodSettingCurrencyModel' => false, 'PPLCZ\\Model\\Model\\ShipmentMethodSettingPriceRuleModel' => false, 'PPLCZ\\Model\\Model\\ShipmentMethodSettingWeightRuleModel' => false, '\\Jane\\Component\\JsonSchemaRuntime\\Reference' => false);
    }
}