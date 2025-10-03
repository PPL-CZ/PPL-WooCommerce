<?php
namespace PPLCZ\ModelCPLNormalizer;

use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModel;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelInsurance;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelSender;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery;
use PPLCZ\Data\AddressData;
use PPLCZ\Data\PackageData;
use PPLCZ\Data\ParcelData;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Serializer;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;


class CPLBatchShipmentDenormalizer implements DenormalizerInterface
{
    public const INTEGRATOR = "4546462";

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof ShipmentData)
        {
            if ($type === EpsApiMyApi2WebModelsShipmentBatchShipmentModel::class) {
                $shipmentBatch = new EpsApiMyApi2WebModelsShipmentBatchShipmentModel();
                $shipmentBatch->setReferenceId($data->get_reference_id());
                $shipmentBatch->setProductType($data->get_service_code());
                if ($data->get_cod_value()) {
                    $shipmentBatch->setCashOnDelivery(Serializer::getInstance()->denormalize($data, EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery::class));
                }
                $shipmentBatch->setIntegratorId(self::INTEGRATOR);
                $shipmentBatch->setReferenceId($data->get_reference_id());
                $shipmentBatch->setNote($data->get_note());

                if ($data->get_sender_address_id() === null)
                {
                    $addresses = AddressData::get_default_sender_addresses();
                    if ($addresses)
                    {
                        $data->set_sender_address_id($addresses[0]->get_id());
                        $data->ignore_lock();
                        $data->save();
                    }
                }
                $shipmentBatch->setSender(Serializer::getInstance()->denormalize(new AddressData($data->get_sender_address_id()), EpsApiMyApi2WebModelsShipmentBatchShipmentModelSender::class));
                $shipmentBatch->setRecipient(Serializer::getInstance()->denormalize(new AddressData($data->get_recipient_address_id()), EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel::class));

                if ($data->get_has_parcel())
                    $shipmentBatch->setSpecificDelivery(Serializer::getInstance()->denormalize($data, EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery::class ));
                if ($data->get_age()) {
                    $shipmentBatch->setAgeCheck('A' . $data->get_age());
                }
                $shipmentBatch->setShipmentSet(Serializer::getInstance()->denormalize($data, EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet::class));

                return $shipmentBatch;
            }
            else if ($type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery::class)
            {
                $cashOnDelivery = new EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery();
                $cashOnDelivery->setCodVarSym($data->get_cod_variable_number());
                $cashOnDelivery->setCodCurrency($data->get_cod_value_currency());
                $cashOnDelivery->setCodPrice($data->get_cod_value());
                return $cashOnDelivery;
            }
            else if ($type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery::class )
            {
                $specific = new EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery();
                $parcel = new ParcelData($data->get_parcel_id());
                $specific->setParcelShopCode($parcel->get_code());
                return $specific;
            }
            else if ($type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelInsurance::class) {
                $ids = $data->get_package_ids();
                $id = reset($ids);
                $package = new PackageData($id);
                if ($package->get_insurance()) {
                    $insurance = new EpsApiMyApi2WebModelsShipmentBatchShipmentModelInsurance();
                    $insurance->setInsurancePrice($package->get_insurance());
                    return $insurance;
                }
                return null;
            }


        }
        throw new \Exception();
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof ShipmentData && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModel::class
                || $data instanceof ShipmentData && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery::class
                || $data instanceof ShipmentData && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery::class
                || $data instanceof ShipmentData && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelInsurance::class;
    }
}