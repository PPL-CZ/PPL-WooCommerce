<?php

namespace PPLCZ\ModelNormalizer;

defined("WPINC") or die();

use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Traits\ParcelDataModelTrait;

class  OrderAddressDataDenormalizer implements DenormalizerInterface
{

    use ParcelDataModelTrait;

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        $order = new \WC_Order($data->get_wc_order_id());
        $address = new Address();

        $parcelshop = self::getParcelshopOrderData($order);
        if ($parcelshop) {
            $address->setAddress($parcelshop->getStreet());
            $address->setCity($parcelshop->getCity());
            $address->setName($parcelshop->getName());
            $address->setType("parcelshop");
        } else {
            $address->setAddress($order->get_shipping_address_1());
            $address->setCity($order->get_shipping_city());
            $address->setName($order->get_shipping_first_name() . " " . $order->get_shipping_last_name());
            $address->setType("other");
        }

        return $address;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof ShipmentData && $type === Address::class;
    }
}