<?php
namespace PPLCZ\Traits;
defined("WPINC") or die();

use PPLCZ\Model\Model\ParcelDataModel;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Serializer;
use PPLCZ\ShipmentMethod;

trait ParcelDataModelTrait {


    /**
     * @param \WC_Order $order
     * @return ?\WC_Order_Item_Shipping
     */
    public static function hasPPLShipment(\WC_Order $order) {
        /**
         * @var  \WC_Order_Item_Shipping[] $shippingMethods
         */
        $shippingMethods = $order->get_shipping_methods();

        foreach ($shippingMethods as $shippingMethod) {
            if (str_contains($shippingMethod->get_method_id(), pplcz_create_name(""))) {
                return $shippingMethod;
            }
        }
        return null;
    }


    public static function setParcelshopOrderData(\WC_Order $order, ?ParcelDataModel $data)
    {
        /**
         * @var  \WC_Order_Item_Shipping[] $shippingMethods
         */
        $shippingMethods = $order->get_shipping_methods();

        foreach ($shippingMethods as $shippingMethod) {
            if ($shippingMethod->get_method_id() === pplcz_create_name("")) {
                $method = new ShipmentMethod($shippingMethod->get_instance_id());
                if ($method->parcelBoxRequired)
                    $shippingMethod->update_meta_data(pplcz_create_name("parcelshop_data"), $data ? Serializer::getInstance()->normalize($data, "array") : null);
            }
        }
    }


    public static function getParcelshopOrderData(\WC_Order $order, $ifActive = false) : ?ParcelDataModel {
        /**
         * @var  \WC_Order_Item_Shipping[] $shippingMethods
         */
        $shippingMethods = $order->get_shipping_methods();

        if ($order instanceof \WC_Order) {
            foreach ($shippingMethods as $shippingMethod) {
                if (str_contains($shippingMethod->get_method_id(),  pplcz_create_name(""))) {
                    $data = self::getParcelDataModel($shippingMethod);
                    return $data;
                }
            }
        }
        return null;
    }

    public static function setParcelDataModel(\WC_Order_Item_Shipping $shipping, ?ParcelDataModel $data)
    {
        $shipping->update_meta_data(pplcz_create_name("parcelshop_data"), $data ? Serializer::getInstance()->normalize($data, "array") : null);
    }

    public static function getParcelDataModel(\WC_Order_Item_Shipping $shipping): ?ParcelDataModel
    {
        $meta = $shipping->get_meta(pplcz_create_name("parcelshop_data"));
        if ($meta)
            return Serializer::getInstance()->denormalize($meta, ParcelDataModel::class);
        return null;
    }
}