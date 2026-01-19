<?php
namespace PPLCZ\Validator;

use PPLCZ\Admin\CPLOperation;
use PPLCZ\Model\Model\ProductModel;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Proxy\ApiCartProxy;
use PPLCZ\Serializer;
use PPLCZ\Setting\MethodSetting;
use PPLCZ\ShipmentMethod;
use PPLCZ\Traits\ParcelDataModelTrait;
use PPLCZCPL\ApiException;
use WpOrg\Requests\Exception;

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

    private static $accessPoints = [];

    public function validate($model, $errors, $path)
    {

        $model = $model instanceof  \WC_Cart ? new ApiCartProxy($model) : $model;

        /**
         * @var ApiCartProxy $model
         * @var \WC_Shipping_Rate $shippingMethod
         */
        $shippingMethod = pplcz_get_cart_shipping_method();
        if (!$shippingMethod)
            return $errors;


        /**
         * @var CartModel $data
         */
        $data = pplcz_denormalize($shippingMethod->get_meta_data(), CartModel::class);

        $parcel = pplcz_get_cart_parceldata();

        if ($parcel) {
            if (!isset(self::$accessPoints[$parcel->getCode()])) {
                self::$accessPoints[$parcel->getCode()] = true;
                try {
                    $cpl = new CPLOperation();
                    $accessToken = $cpl->getAccessToken();
                    if ($accessToken) {
                        $testparcel = $cpl->findParcel($parcel->getCode(), $parcel->getCountry(), 10);
                        if (!$testparcel)
                            self::$accessPoints[$parcel->getCode()] = false;
                    }
                } catch (\Exception $ex2) {
                    if ($ex2 instanceof ApiException && $ex2->getCode() === 404) {
                        self::$accessPoints[$parcel->getCode()] = false;
                    }
                }
            }

            if (!self::$accessPoints[$parcel->getCode()])
            {
                $errors->add("parcelshop-disabled-shop", __("Vybrané výdejní místo se nepodařilo najít", "ppl-cz"));
                return $errors;
            }


            switch ($parcel->getAccessPointType()) {
                case 'ParcelShop':
                    if (!$data->getParcelShopEnabled())
                        $errors->add("parcelshop-disabled-shop", __("V košíku je produkt, který neumožňuje vybrat obchod pro vyzvednutí zásilky", "ppl-cz"));
                    break;
                case 'ParcelBox':
                    if (!$data->getParcelBoxEnabled())
                        $errors->add("parcelshop-disabled-box", __("V košíku je produkt, který neumožňuje vybrat ParcelBox pro vyzvednutí zásilky", "ppl-cz"));
                    break;
                case 'AlzaBox':
                    if (!$data->getAlzaBoxEnabled())
                        $errors->add("parcelshop-disabled-box", __("V košíku je produkt, který neumožňuje vybrat AlzaBox pro vyzvednutí zásilky", "ppl-cz"));
                    break;
                default:
                    $errors->add("parcelshop-disabled-box", __("V košíku je produkt, který neumožňuje vybrat box pro vyzvednutí zásilky", "ppl-cz"));
            }

            $country = $parcel->getCountry();

            if (!in_array($country, $data->getEnabledParcelCountries(),true))
            {
                $errors->add("parcelshop-disabled-country", __("Nepovolená země výdejního místa", "ppl-cz"));
            }
        }

        if ($data->getParcelRequired() && !$parcel) {
            $errors->add("parcelshop-missing", __("Je potřeba vybrat výdejní místo pro doručení zásilky", "ppl-cz"));
        }

        if (static::ageRequired($model->cart, $shippingMethod) // pozaduji vek
            && ($parcel && $parcel->getAccessPointType() !== 'ParcelShop')) // vybrana parcela a neni to obchod
        {
            $errors->add("parcelshop-age-required", __("Z důvodu kontroly věku je nutné vybrat obchod, ne výdejní box", "ppl-cz"));
        }


        if (!$model->getPhone()) {
            $errors->add("parcelshop-phone-required", __("Pro zasílání informací o stavu zásilky je nutno vyplnit telefonní číslo", "ppl-cz"));
        }
        else if (!self::isPhone($model->getPhone()))
        {
            $errors->add("parcelshop-phone-required", __("Nevalidní telefonní číslo", "ppl-cz"));
        }

        if (!self::isZip($model->getCountry(), $model->getZip())) {
            $errors->add("parcelshop-shippingzip-required", __("Nevalidní PSČ u doručovací adresy", "ppl-cz"));
        }

        if ($data->getParcelRequired() && $model->getCountry() && $parcel)
        {
            $country = $model->getCountry();
            if ($country !== $parcel->getCountry())
            {
                $errors->add("parcelshop-shippingzip-required", __("Země kontaktní (doručovací) adresy je jiná, než výdejního místa", "ppl-cz"));
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