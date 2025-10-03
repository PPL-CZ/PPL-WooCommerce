<?php
namespace PPLCZ\ModelNormalizer;

defined("WPINC") or die();

use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZ\Data\PackageData;
use PPLCZ\Model\Model\PackageModel;

class PackageModelDernomalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof PackageData) {
            $model = new PackageModel();
            if($data->get_id())
                $model->setId($data->get_id());
            if ($data->get_weight())
                $model->setWeight($data->get_weight());
            if ($data->get_insurance_currency())
                $model->setInsuranceCurrency($data->get_insurance_currency());
            if ($data->get_insurance())
                $model->setInsurance($data->get_insurance());

            if ($data->get_phase_label())
                $model->setPhaseLabel($data->get_phase_label());
            else
                $model->setPhaseLabel("");

            $model->setPhase($data->get_phase() ?: "None");

            if ($data->get_reference_id())
                $model->setReferenceId($data->get_reference_id());


            if ($data->get_ignore_phase())
                $model->setIgnorePhase($data->get_ignore_phase());
            if ($data->get_last_update_phase())
                $model->setLastUpdatePhase($data->get_last_update_phase());

            $model->setShipmentNumber($data->get_shipment_number() ?: "");
            $model->setLabelId($data->get_label_id() ?: "");
            $model->setImportError($data->get_import_error() ?: "");
            $model->setImportErrorCode($data->get_import_error_code() ?: "");

            return $model;
        } else if ($data instanceof  PackageModel) {
            $model = $context['data'] ?? new PackageData();
            if ($model->get_lock())
                $model = new PackageData();

            if ($data->isInitialized("referenceId"))
                $model->set_reference_id("{$data->getReferenceId()}");

            $model->set_weight($data->getWeight() ?: null);
            $model->set_insurance_currency($data->getInsuranceCurrency() ?: null);
            $model->set_insurance($data->getInsurance() ?: null);
            return $model;
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        if ($data instanceof PackageData && $type === PackageModel::class)
            return true;

        if ($data instanceof PackageModel && $type === PackageData::class)
        {
            return true;
        }
        return false;
    }
}