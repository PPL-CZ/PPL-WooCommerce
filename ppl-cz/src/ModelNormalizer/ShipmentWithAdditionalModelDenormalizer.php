<?php

namespace PPLCZ\ModelNormalizer;


use PPLCZ\Data\OrderProxy;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\ShipmentWithAdditionalModel;
use PPLCZ\Model\Model\WpErrorModel;
use PPLCZ\Repository\Normalizer;
use PPLCZ\TLoader;
use PPLCZ\Validator\Validator;
use PPLCZ\Validator\WP_Error;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ShipmentWithAdditionalModelDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        $shipmentModel = pplcz_denormalize($data, ShipmentModel::class);
        $errors = new \WP_Error();

        $shipment = new ShipmentWithAdditionalModel();
        $shipment->setShipment($shipmentModel);


        pplcz_validate($shipmentModel, $errors);

        if ($errors->errors)
        {
            $errors = pplcz_denormalize($errors, WpErrorModel::class . '[]');
            $shipment->setErrors($errors);
        }

        return $shipment;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        if (($data instanceof ShipmentData || $data instanceof \WC_Order)
            && $type === ShipmentWithAdditionalModel::class)
        {
            return true;
        }
        return false;
    }
}