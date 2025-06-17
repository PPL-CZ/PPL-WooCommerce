<?php

namespace PPLCZ\ModelNormalizer;

defined("WPINC") or die();

use PPLCZ\Model\Model\CalculatedDPH;
use PPLCZ\Model\Model\CategoryModel;
use PPLCZ\Model\Model\ProductModel;
use PPLCZ\Model\Model\ShipmentMethodSettingCurrencyModel;
use PPLCZ\Model\Model\ShipmentMethodSettingModel;
use PPLCZ\Serializer;
use PPLCZ\Validator\CartValidator;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZ\Front\Validator\ParcelShopValidator;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\ShipmentMethod;

class CartModelDernomalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        /**
         * @var ShipmentMethod $data
         * @var ShipmentMethodSettingModel $setting
         */

        $setting = pplcz_denormalize($data, ShipmentMethodSettingModel::class);


        if (!WC()->session)
            WC()->initialize_session();

        if (!WC()->cart)
            WC()->initialize_cart();

        $cart = WC()->cart;
        $session = WC()->session;


        $country = $cart->get_customer()->get_shipping_country('');

        $paymentMethod = WC()->session->get('chosen_payment_method');

        $currency = get_woocommerce_currency();

        $currencySetting = null;

        if ($setting->getCurrencies())
        {
            $currencySetting = array_filter($setting->getCurrencies(), function ($item) use ($currency)
            {
               return $item->getCurrency() ===  $currency;
            });
            $currencySetting = reset($currencySetting);
        }

        if ($currencySetting == null)
        {
            $currencySetting = new ShipmentMethodSettingCurrencyModel();
            $currencySetting->setCurrency($currency);
            $currencySetting->setEnabled(false);
        }

        $shipmentCartModel = new CartModel();

        $countries = include __DIR__ . '/../config/countries.php';
        $limits = include __DIR__ . '/../config/limits.php';

        $shipmentCartModel->setParcelRequired(false);
        $shipmentCartModel->setMapEnabled(false);
        $shipmentCartModel->setDisabledByCountry(false);
        $shipmentCartModel->setAgeRequired(false);
        $shipmentCartModel->setDisabledByRules(false);

        $disabledParcelBox = !!get_option(pplcz_create_name("disabled_parcelbox"));
        $disabledParcelShop = !!get_option(pplcz_create_name("disabled_parcelshop"));
        $disabledAlzaBox = !!get_option(pplcz_create_name("disabled_alzabox"));


        if ($setting->getParcelBoxes()) {
            $shipmentCartModel->setMapEnabled(true);
            $shipmentCartModel->setParcelRequired(true);
            /**
             * ppl ma vzdy 4 zeme, CR, PL, DE, SK
             *
             * 1. nactu 4 zeme
             * 2. zeptam se, ktere jsou disablovane [PL, DE]
             *
             * [CR, PL, DE, SK] - [PL, DE] = [CR, SK]
             *
             * je zmene v [CR, SK]?
             *
             */
            // ziskani zemi s parcelshopy
            $countriesWithParcelshop = array_keys(pplcz_get_parcel_countries());

            // ziskani nepovelnych zemi s nastaveni dopravy a zakladniho nastaveni
            $disabledCountriesFromShipmentSetting = $setting->getDisabledParcelCountries() ?? [];
            $disabledCountriesFromBaseSetting = get_option(pplcz_create_name("disabled_parcel_countries"));
            if (!is_array($disabledCountriesFromBaseSetting))
                $disabledCountriesFromBaseSetting = [];
            $disabledCountries = array_merge($disabledCountriesFromBaseSetting, $disabledCountriesFromShipmentSetting);

            /**
             * @var \WC_Shipping_Zone[] $zones
             */
            $zones = \WC_Shipping_Zones::get_zones();
            $data_countries = [];
            foreach ($zones as $zone)
            {
                foreach ($zone["shipping_methods"] as $method) {
                    if ($method->instance_id == $data->instance_id)
                    {
                        $data_countries = array_map(function ($item) { return $item->code; }, $zone["zone_locations"]);
                    }
                }
            }


            // zikani povolenych zemi pomoci mnozinove operace minus (viz vyse)
            $allowedParcelCountries = array_diff($countriesWithParcelshop, $disabledCountries);
            // zeme musi byt podporovana i wp zonou
            $allowedParcelCountries = array_intersect($allowedParcelCountries, $data_countries);
            // je povolena zeme, kteoru ted mame v ramci parcelshopu?
            $enabledByCountry = in_array($country, $allowedParcelCountries, true);

            $shipmentCartModel->setEnabledParcelCountries($allowedParcelCountries);

            $shipmentCartModel->setParcelShopEnabled($enabledByCountry && !$disabledParcelShop && !$setting->getDisabledParcelShop());
            $shipmentCartModel->setParcelBoxEnabled($enabledByCountry && !$disabledParcelBox && !$setting->getDisabledParcelBox());
            $shipmentCartModel->setAlzaBoxEnabled($enabledByCountry && !$disabledAlzaBox && !$setting->getDisabledAlzaBox());
            $shipmentCartModel->setDisabledByCountry(!$enabledByCountry);

        }
        else {
            $shipmentCartModel->setDisabledByCountry(false);
            $shipmentCartModel->setParcelBoxEnabled(false);
            $shipmentCartModel->setParcelShopEnabled(false);
            $shipmentCartModel->setAlzaBoxEnabled(false);
        }

        $shipmentCartModel->setDisabledByWeight(true);

        $totalWeight = $cart->get_cart_contents_weight();

        $selectedWeightPrice = 0;

        if ($setting->getCostByWeight()) {
            $selectedWeightRule = null;
            foreach ($setting->getWeights() as $weight) {
                if (($weight->getFrom() == null || $weight->getFrom() <= $totalWeight)
                    && ($weight->getTo() == null || $weight->getTo() > $totalWeight)) {
                    //["EUR" => 6, "CZK" => 5]. ["CZK" => 5], 5
                    // [] => null
                    $weightPrice = array_filter($weight->getPrices(), function ($price) use ($currency) {
                        return $price->getCurrency() === $currency;
                    });
                    $weightPrice = reset($weightPrice);
                    if ($weightPrice && $selectedWeightPrice < $weightPrice->getPrice()) {
                        $selectedWeightPrice = $weightPrice->getPrice() ?: 0;
                        $shipmentCartModel->setDisabledByWeight(false);
                        $selectedWeightRule = $weight;
                    }
                }
            }

            if ($selectedWeightRule) {
                $shipmentCartModel->setAlzaBoxEnabled($shipmentCartModel->getAlzaBoxEnabled() && !$selectedWeightRule->getDisabledAlzaBox());
                $shipmentCartModel->setParcelBoxEnabled($shipmentCartModel->getParcelBoxEnabled() && !$selectedWeightRule->getDisabledParcelBox());
                $shipmentCartModel->setParcelShopEnabled($shipmentCartModel->getParcelShopEnabled() && !$selectedWeightRule->getDisabledParcelShop());
            }
        } else {
            $selectedWeightRule = array_filter($setting->getCurrencies(), function($currencies ) use ($currency) {
                return $currencies->getCurrency() === $currency;
            });

            $selectedWeightRule = reset($selectedWeightRule);
            if ($selectedWeightRule)
            {
                $shipmentCartModel->setDisabledByWeight(false);
                /**
                 * @var ShipmentMethodSettingCurrencyModel  $selectedWeightPrice
                 */
                $selectedWeightPrice = $selectedWeightRule->getCost() ?: 0;
            }
        }



        $serviceCode = str_replace(pplcz_create_name(''), '', $data->id);
        // preklad
        $serviceCode = ShipmentMethod::methodsFor($country, $serviceCode);

        if (CartValidator::ageRequired(WC()->cart, $serviceCode)) {
            $shipmentCartModel->setAgeRequired(true);
            $shipmentCartModel->setParcelBoxEnabled(false);
            $shipmentCartModel->setAlzaBoxEnabled(false);
        }

        if (!isset($countries[$country]))
        {
            $shipmentCartModel->setDisabledByCountry(true);
        }


        $shipmentCartModel->setServiceCode($serviceCode);

        $codServiceCode = ShipmentMethod::codMethods()[$serviceCode];


        $shipmentCartModel->setCodPayment($setting->getCodPayment() ?: '');

        $disabledPayments = $setting->getDisablePayments();

        $shipmentCartModel->setDisablePayments($disabledPayments);

        $countryAndBankAccount = pplcz_get_cod_currencies();

        $accountIn = array_filter($countryAndBankAccount, function ($item) use ($country, $currency) {
            return $item['country'] == $country && $item['currency'] == $currency;
        });

        if (!isset($countries[$country]) || !$countries[$country] || !$accountIn)
            $shipmentCartModel->setDisableCod(true);
        else
            $shipmentCartModel->setDisableCod(false);

        $maxCodPrice = array_values(array_filter($limits['COD'], function ($item) use ($country, $codServiceCode, $currency) {
            if ($item['product'] === $codServiceCode && $item['currency'] === $currency && $item['country'] === $country) {
                return true;
            }
            return false;
        }, true));



        $totalContents = $cart->get_cart_contents_total() + $cart->get_cart_contents_tax();

        $total = $totalContents;

        $isPriceWithDph = $setting->getIsPriceWithDph();

        $shipmentCartModel->setIsPriceWithDph($isPriceWithDph);

        if (!$maxCodPrice) {
            $shipmentCartModel->setDisableCod(true);
            if ($currencySetting->getCostOrderFree() != null
                && $currencySetting->getCostOrderFree() <= $total) {
                $shipmentCartModel->setCodFee(0);
                $shipmentCartModel->setCost(0);
            } else {
                $shipmentCartModel->setCodFee(0);
                $shipmentCartModel->setCost($selectedWeightPrice ?: 0);
            }
        } else {
            $max = @$maxCodPrice[0]['max'];
            if ($max !== '' && $max !== null && $total >= $max) {
                $shipmentCartModel->setDisableCod(true);
                $shipmentCartModel->setCodFee(100000);
                $shipmentCartModel->setCost(100000);
            } else {
                $isCod = $paymentMethod === $shipmentCartModel->getCodPayment();
                $freeCodPrice = $currencySetting->getCostOrderFreeCod();

                if ($isCod
                    && $freeCodPrice != null
                    && $freeCodPrice <= $total) {
                    if ($currencySetting->getCostCodFeeAlways())
                        $shipmentCartModel->setCodFee($currencySetting->getCostCodFee() ?: 0);
                    else
                        $shipmentCartModel->setCodFee(0);
                    $shipmentCartModel->setCost(0);
                } else if ($isCod) {
                    $shipmentCartModel->setCodFee($currencySetting->getCostCodFee() ?: 0);
                    $shipmentCartModel->setCost($selectedWeightPrice ?: 0);
                } else {
                    $shipmentCartModel->setCodFee(0);
                    $costorderfree = $currencySetting->getCostOrderFree();

                    if ($costorderfree != null && floatval($costorderfree) <= $total)
                        $shipmentCartModel->setCost(0);
                    else
                        $shipmentCartModel->setCost($selectedWeightPrice ?: 0);
                }
            }
        }

        $shipmentCartModel->setDisabledByProduct(false);

        foreach (WC()->cart->get_cart() as $key => $cart_item) {
            $product_id = $cart_item['product_id'];
            $product = new \WC_Product($product_id);
            /**
             * @var ProductModel $productModel
             * @var CategoryModel $categoryModel
             */
            $productModel = Serializer::getInstance()->denormalize($product, ProductModel::class);

            if (in_array($codServiceCode, $productModel->getPplDisabledTransport() ?? [], true)) {
                $shipmentCartModel->setDisableCod(true);
            }

            if (in_array($serviceCode, $productModel->getPplDisabledTransport() ?? [], true)) {
                $shipmentCartModel->setDisabledByProduct(true);
                break;
            }

            $shipmentCartModel->setParcelBoxEnabled( !$productModel->getPplDisabledParcelBox() && $shipmentCartModel->getParcelBoxEnabled() );
            $shipmentCartModel->setParcelShopEnabled(!$productModel->getPplDisabledParcelShop() && $shipmentCartModel->getParcelShopEnabled() );
            $shipmentCartModel->setAlzaBoxEnabled(!$productModel->getPplDisabledAlzaBox() && $shipmentCartModel->getAlzaBoxEnabled() );

            $get_parents = $product->get_category_ids();
            $ids = [];

            while ($get_parents) {
                $curId = array_shift($get_parents);
                if (in_array($curId, $ids)) {
                    continue;
                }
                $ids[] = $curId;
                $parId = wp_get_term_taxonomy_parent_id($curId, 'product_cat');
                if ($parId)
                    $get_parents[] = $parId;
            }

            foreach ($ids as $category_id) {
                $term = get_term($category_id);
                $categoryModel = Serializer::getInstance()->denormalize($term, CategoryModel::class);

                if (in_array($codServiceCode, $categoryModel->getPplDisabledTransport() ?? [], true)) {
                    $shipmentCartModel->setDisableCod(true);
                }
                if (in_array($serviceCode, $categoryModel->getPplDisabledTransport() ?? [], true)) {
                    $shipmentCartModel->setDisabledByProduct(true);
                    break 2;
                }

                $shipmentCartModel->setParcelBoxEnabled(!$categoryModel->getPplDisabledParcelBox() && $shipmentCartModel->getParcelBoxEnabled() );
                $shipmentCartModel->setParcelShopEnabled(!$categoryModel->getPplDisabledParcelShop() && $shipmentCartModel->getParcelShopEnabled() );
                $shipmentCartModel->setAlzaBoxEnabled(!$categoryModel->getPplDisabledAlzaBox() && $shipmentCartModel->getAlzaBoxEnabled() );
            }
        }

        if (!$shipmentCartModel->getParcelShopEnabled() && !$shipmentCartModel->getParcelBoxEnabled() && !$shipmentCartModel->getAlzaBoxEnabled() && $shipmentCartModel->getParcelRequired())
        {
            $shipmentCartModel->setDisabledByRules(true);
        }

        if (!$shipmentCartModel->getParcelShopEnabled() && $shipmentCartModel->getAgeRequired())
        {
            $shipmentCartModel->setDisabledByRules(true);
        }

        $shipmentCartModel->setMapEnabled(
            ($shipmentCartModel->getParcelShopEnabled() || $shipmentCartModel->getParcelBoxEnabled() || $shipmentCartModel->getAlzaBoxEnabled())
            && !$shipmentCartModel->getDisabledByCountry()
        );

        // kupony
        $coupons = WC()->cart->get_applied_coupons();
        foreach ($coupons as $coupon)
        {
            $coupon = new \WC_Coupon($coupon);
            if ($coupon->get_id() && $coupon->get_free_shipping())
            {
                $shipmentCartModel->setCost(0);
            }
        }

        // -----------------------------------------------------------------------------------------------------------------------

        $isPriceWithDph = $shipmentCartModel->getIsPriceWithDph();

        if ($data->is_taxable() && $isPriceWithDph) {
            $selected_rates = \WC_Tax::get_shipping_tax_rates();

            $default_shipping_tax_class = get_option('woocommerce_shipping_tax_class');
            // seznam možných daní
            $shipping_rates = array_merge(wp_list_pluck(\WC_Tax::get_tax_rate_classes(), "slug"), [""]);
            foreach ($shipping_rates as $key => $rates) {
                $shipping_rates[$key] = \WC_Tax::get_rates($rates) + ["slug" => $rates];
            }
            // vyhledání daňové sazby navázanou na dopravu
            foreach ($shipping_rates as $specific_rate) {
                foreach ($specific_rate as $rateKey => $globalRate) {
                    if ($rateKey === key($selected_rates)) {
                        $default_shipping_tax_class = $specific_rate['slug'];
                        break 2;
                    }
                }
            }
            $shipmentCartModel->setTaxableName($default_shipping_tax_class);


            $priceWithDph = \WC_Tax::calc_shipping_tax($shipmentCartModel->getCost(), \WC_Tax::get_shipping_tax_rates());
            $selectedWeightPrice = $shipmentCartModel->getCost();
            if ($selectedWeightPrice && $priceWithDph) {
                $first = reset($priceWithDph);
                if ($first) {
                    $procento = ($selectedWeightPrice + $first) / $selectedWeightPrice;
                    $selectedWeightPrice /= $procento;
                }
                $priceWithDph = \WC_Tax::calc_shipping_tax($selectedWeightPrice, \WC_Tax::get_shipping_tax_rates());
                $costDPH = new CalculatedDPH();
                $costDPH->setValue(reset($priceWithDph));
                $costDPH->setDphId(key($priceWithDph));
                $shipmentCartModel->setCostDPH($costDPH);
            }
            $shipmentCartModel->setCost($selectedWeightPrice);

            if ($shipmentCartModel->getCodFee())
            {
                $codFeeDPH = \WC_Tax::calc_shipping_tax($shipmentCartModel->getCodFee(), \WC_Tax::get_shipping_tax_rates());

                if($codFeeDPH){
                    $first = reset($codFeeDPH);
                    $codFee = $shipmentCartModel->getCodFee();
                    if ($first) {
                        $procento = ($first + $codFee) / $codFee;
                        $codFee /= $procento;
                    }
                    $codFeeDPH = \WC_Tax::calc_shipping_tax($codFee, \WC_Tax::get_shipping_tax_rates());
                    $costDPH = new CalculatedDPH();
                    $costDPH->setValue(reset($codFeeDPH));
                    $costDPH->setDphId(key($codFeeDPH));
                    $shipmentCartModel->setCodFeeDPH($costDPH);
                    $shipmentCartModel->setCodFee($codFee);
                }

            }

            //   $shipmentCartModel->setTaxes(honta)
          //  $shipmentCartModel->setCost($priceWithDph ?: 0);
        }

        // -------------------------------------------------------------------------------------------------------------------

        return $shipmentCartModel;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        if ($data instanceof ShipmentMethod && $type === CartModel::class) {
            return true;
        }
        return false;
    }
}