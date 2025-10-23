<?php
namespace PPLCZ;

defined("WPINC") or die();

use PPLCZ\Model\Model\ErrorLogModel;
use PPLCZ\ModelCPLNormalizer\CPLBatchAddressDenormalizer;
use PPLCZ\ModelCPLNormalizer\CPLBatchCreateShipmentsDenormalizer;
use PPLCZ\ModelCPLNormalizer\CPLBatchPackageDenormalizer;
use PPLCZ\ModelCPLNormalizer\CPLBatchShipmentDenormalizer;
use PPLCZ\Model\Normalizer\JaneObjectNormalizer;
use PPLCZ\ModelNormalizer\AddressModelDenormalizer;
use PPLCZ\ModelNormalizer\BankModelDenormalizer;
use PPLCZ\ModelNormalizer\BatchModelDenormalizer;
use PPLCZ\ModelNormalizer\CategoryModelDenormalizer;
use PPLCZ\ModelNormalizer\ErrorLogDenormalizer;
use PPLCZ\ModelNormalizer\OrderAddressDataDenormalizer;
use PPLCZ\ModelNormalizer\PackageModelDernomalizer;
use PPLCZ\ModelNormalizer\ParcelDataModelDenormalizer;
use PPLCZ\ModelNormalizer\ProductModelDenormalizer;
use PPLCZ\ModelNormalizer\CartModelDernomalizer;
use PPLCZ\ModelNormalizer\ShipmentDataDenormalizer;
use PPLCZ\ModelNormalizer\CollectionDataDenormalizer;
use PPLCZ\ModelNormalizer\ShipmentSettingDenormalizer;
use PPLCZ\ModelNormalizer\ShipmentWithAdditionalModelDenormalizer;
use PPLCZ\ModelNormalizer\WpErrorModelDenormalizer;

class Serializer extends \PPLCZVendor\Symfony\Component\Serializer\Serializer {
    public function __construct(array $normalizers = [], array $encoders = [])
    {
        parent::__construct([
            new ShipmentDataDenormalizer(),
            new CollectionDataDenormalizer(),
            new OrderAddressDataDenormalizer(),
            new AddressModelDenormalizer(),
            new PackageModelDernomalizer(),
            new ShipmentDataDenormalizer(),
            new ParcelDataModelDenormalizer(),
            new ProductModelDenormalizer(),
            new CartModelDernomalizer(),
            new CategoryModelDenormalizer(),
            new ShipmentSettingDenormalizer(),
            new ErrorLogDenormalizer(),
            new WpErrorModelDenormalizer(),
            new ShipmentWithAdditionalModelDenormalizer(),
            new BatchModelDenormalizer(),
            // cpl
            new CPLBatchAddressDenormalizer(),
            new CPLBatchCreateShipmentsDenormalizer(),
            new CPLBatchPackageDenormalizer(),
            new CPLBatchShipmentDenormalizer(),

            // vygenerovano
            new JaneObjectNormalizer(),

        ], $encoders);
    }

    protected static $instance;

    public static function getInstance() {
        return self::$instance ?: (self::$instance = new self());
    }
}