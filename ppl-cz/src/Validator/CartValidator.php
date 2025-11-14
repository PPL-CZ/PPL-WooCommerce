<?php
namespace PPLCZ\Validator;

use PPLCZ\Model\Model\ProductModel;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Proxy\ApiCartProxy;
use PPLCZ\Serializer;
use PPLCZ\Setting\MethodSetting;
use PPLCZ\ShipmentMethod;
use PPLCZ\Traits\ParcelDataModelTrait;

class CartValidator extends ModelValidator {

    use ParcelDataModelTrait;

    public function canValidate($model)
    {
        if ($model instanceof \WC_Cart)
            return true;
        if ($model instanceof  ApiCartProxy)
            return true;
        return false;
    }

    public function validate($model, $errors, $path)
    {

        $model = $model instanceof  \WC_Cart ? new ApiCartProxy($model) : $model;

        /**
         * @var ApiCartProxy $model
         * @var \WC_Shipping_Rate $shippingMethod
         */
        $shippingMethod = pplcz_get_cart_shipping_method();
        if (!$shippingMethod)
            return;


        /**
         * @var CartModel $data
         */
        $data = pplcz_denormalize($shippingMethod->get_meta_data(), CartModel::class);


        $parcel = pplcz_get_cart_parceldata();
        if ($parcel) {
            switch ($parcel->getAccessPointType()) {
                case 'ParcelShop':
                    if (!$data->getParcelShopEnabled())
                        $errors->add("parcelshop-disabled-shop", "V košíku produkt, který neumožňuje vybrat obchod pro vyzvednutí zásilky");
                    break;
                case 'ParcelBox':
                    if (!$data->getParcelBoxEnabled())
                        $errors->add("parcelshop-disabled-box", "V košíku produkt, který neumožňuje vybrat ParcelBox pro vyzvednutí zásilky");
                    break;
                case 'AlzaBox':
                    if (!$data->getAlzaBoxEnabled())
                        $errors->add("parcelshop-disabled-box", "V košíku produkt, který neumožňuje vybrat AlzaBox pro vyzvednutí zásilky");
                    break;
                default:
                    $errors->add("parcelshop-disabled-box", "V košíku produkt, který neumožňuje vybrat box pro vyzvednutí zásilky");
            }

            $country = $parcel->getCountry();

            if (!in_array($country, $data->getEnabledParcelCountries(),true))
            {
                $errors->add("parcelshop-disabled-country", "Nepovolená země výdejního místa");
            }
        }

        if ($data->getParcelRequired() && !$parcel) {
            $errors->add("parcelshop-missing", "Je potřeba vybrat výdejní místo pro doručení zásilky");
        }

        if (static::ageRequired($model->cart, $shippingMethod)
            && (!$parcel || $parcel->getAccessPointType() !== 'ParcelShop'))
        {
            $errors->add("parcelshop-age-required", "Z důvodu kontroly věku je nutné vybrat obchod, ne výdejní box.");
        }


        if (!$model->getPhone()) {
            $errors->add("parcelshop-phone-required", "Pro zasílání informací o stavu zásilky je nutno vyplnit telefonní číslo.");
        }
        else if (!self::isPhone($model->getPhone()))
        {
            $errors->add("parcelshop-phone-required", "Nevalidní telefonní číslo");
        }

        if (!self::isZip($model->getCountry(), $model->getZip())) {
            $errors->add("parcelshop-shippingzip-required", "Nevalidní PSČ u doručovací adresy");
        }

        if ($data->getParcelRequired() && $model->getCountry() && $parcel)
        {
            $country = $model->getCountry();
            if ($country !== $parcel->getCountry())
            {
                $errors->add("parcelshop-shippingzip-required", "Země kontaktní (doručovací) adresy je jiná, než výdejního místa");
            }
        }
        return $errors;
    }


    public static function ageRequired(\WC_Cart $cart, $shippingMethod) {
        if (is_string($shippingMethod))
        {
            $methodid = $shippingMethod;
        }
        else
        {
            $methodid = $shippingMethod->get_method_id();
            $methodid = str_replace(pplcz_create_name(""), "", $methodid);
        }

        $methodid = MethodSetting::getMethodForCountry($cart->get_customer()->get_shipping_country(), $methodid);

        if (in_array($methodid, ["SMAR", "SMAD"], true)) {
            foreach ($cart->get_cart() as $key => $val) {
                $product = $val['product_id'];
                $variation = $val['variation'];
                if (array_reduce([$product, $variation], function ($carry, $item) {
                    if ($carry || !$item)
                        return $carry;

                    $variation = new \WC_Product($item);
                    /**
                     * @var ProductModel $model
                     */
                    $model = Serializer::getInstance()->denormalize($variation, ProductModel::class);
                    if ($model->getPplConfirmAge18()
                        || $model->getPplConfirmAge15()) {
                        $carry = true;
                    }
                    return $carry;
                }, false)) {
                    return true;
                }
            }
        }
        return false;
    }

}