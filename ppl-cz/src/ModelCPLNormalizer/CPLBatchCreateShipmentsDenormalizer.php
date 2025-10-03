<?php
namespace PPLCZ\ModelCPLNormalizer;

use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModelLabelSettings;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchLabelSettingsModelCompleteLabelSettings;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModel;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Serializer;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;


class CPLBatchCreateShipmentsDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        $createShipment = new EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModel();
        $throwException = false;
        $createShipment->setShipments(array_map(function ($item) {
            if (!($item instanceof ShipmentData))
                $item = new ShipmentData($item);

            if (!$item->get_id()) {
                throw new \Exception("problem se zasilkou");
            }

            try {
                return Serializer::getInstance()->denormalize($item, EpsApiMyApi2WebModelsShipmentBatchShipmentModel::class);
            }
            catch (\Exception $exception)
            {
                $item->set_import_errors($exception->getMessage());
                $item->save();
            }
        }, $data));
        if ($throwException)
            throw new \Exception("Problém se zásilkama");

        $batch = new EpsApiMyApi2WebModelsShipmentBatchLabelSettingsModelCompleteLabelSettings();
        $batch->setIsCompleteLabelRequested(true);
        $batch->setPageSize("A4");
        $batch->setPosition(1);

        $batchModelLabelSetting = new EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModelLabelSettings();
        $batchModelLabelSetting->setCompleteLabelSettings($batch);
        $batchModelLabelSetting->setFormat("Pdf");
        $createShipment->setLabelSettings($batchModelLabelSetting);
        return $createShipment;

    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $type === EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModel::class
                && is_array($data);
    }
}