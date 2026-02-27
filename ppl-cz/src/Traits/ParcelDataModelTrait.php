<?php
namespace PPLCZ\Traits;
defined("WPINC") or die();

use PPLCZ\Model\Model\AdditionalDataModel;
use PPLCZ\Model\Model\CartDataModel;
use PPLCZ\Model\Model\ParcelDataModel;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Model\Model\ShipmentMethodModel;
use PPLCZ\Serializer;
use PPLCZ\Setting\MethodSetting;
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

    public static function setOrderCartData(\WC_Order $order, ?CartDataModel $data)
    {
        /**
         * @var  \WC_Order_Item_Shipping[] $shippingMethods
         */
        $shippingMethods = $order->get_shipping_methods();

        foreach ($shippingMethods as $shippingMethod) {
            if (str_contains($shippingMethod->get_method_id(), pplcz_create_name(""))) {
                $method = new ShipmentMethod($shippingMethod->get_instance_id());
                $code = str_replace(pplcz_create_name(""), "",$method->id);
                $methodsetting = MethodSetting::getMethod($code);
                if ($methodsetting && $data) {
                    if ($data->getParcelData()) {
                        $shippingMethod->update_meta_data(pplcz_create_name("parcelshop_data"), pplcz_normalize($data->getParcelData()));
                    }
                    if ($data->getAdditionalData()) {
                        $shippingMethod->update_meta_data(pplcz_create_name("additional_data"), pplcz_normalize($data->getAdditionalData()));
                    }
                }
            }
        }
    }


    public static function getOrderCartData(\WC_Order $order, $ifActive = false) : ?CartDataModel {
        /**
         * @var  \WC_Order_Item_Shipping[] $shippingMethods
         */
        $shippingMethods = $order->get_shipping_methods();

        if ($order instanceof \WC_Order) {
            foreach ($shippingMethods as $shippingMethod) {
                if (str_contains($shippingMethod->get_method_id(),  pplcz_create_name(""))) {
                    return self::getOrderItemShippingCartDataModel($shippingMethod);
                }
            }
        }
        return null;
    }



    public static function setOrderShippingCartDataModel(\WC_Order_Item_Shipping $shipping, ?CartDataModel $data)
    {
        $shipping->update_meta_data(pplcz_create_name("parcelshop_data"), $data && $data->getParcelData() ? pplcz_normalize($data->getParcelData()) : null);
        $shipping->update_meta_data(pplcz_create_name("additional_data"), $data && $data->getAdditionalData() ? pplcz_normalize($data->getAdditionalData()) : null);
    }

    public static function getOrderItemShippingCartDataModel(\WC_Order_Item_Shipping $shipping)
    {
        $cartdata = new CartDataModel();

        $meta = $shipping->get_meta(pplcz_create_name("parcelshop_data"));
        if ($meta)
            $cartdata->setParcelData(pplcz_denormalize($meta, ParcelDataModel::class));
        $meta = $shipping->get_meta(pplcz_create_name("additional_data"));
        if ($meta)
            $cartdata->setAdditionalData(pplcz_denormalize($meta, AdditionalDataModel::class));

        return $cartdata;
    }
}