<?php

namespace PPLCZ\ModelNormalizer;

defined("WPINC") or die();

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

        $shipmentCartModel->setParcelShopEnabled(!$disabledParcelShop && !$setting->getDisabledParcelBox());
        $shipmentCartModel->setParcelBoxEnabled(!$disabledParcelBox && !$setting->getDisabledParcelShop());
        $shipmentCartModel->setAlzaBoxEnabled(!$disabledAlzaBox && !$setting->getDisabledAlzaBox());

        $shipmentCartModel->setDisabledByWeight(true);

        $totalWeight = $cart->get_cart_contents_weight();

        $selectedWeightPrice = 0;

        if ($setting->getCostByWeight()) {
            $selectedWeightRule = null;
            foreach ($setting->getWeights() as $weight) {
                if (($weight->getFrom() == null || $weight->getFrom() <= $totalWeight)
                    && ($weight->getTo() == null || $weight->getTo() > $totalWeight)) {
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
                $shipmentCartModel->setParcelShopEnabled($shipmentCartModel->getParcelShopEnabled() && !$selectedWeightRule->getDisabledParcelBox());
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


        if (@$data->parcelBoxRequired) {
            $shipmentCartModel->setParcelRequired(true);
            $shipmentCartModel->setMapEnabled(true);
            if (!in_array($country, ["CZ", "SK", "DE", "PL"], true))
            {
                $shipmentCartModel->setDisabledByCountry(true);
            }
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

        if (!@$countries[$country] || !$accountIn)
            $shipmentCartModel->setDisableCod(true);
        else
            $shipmentCartModel->setDisableCod(false);

        $maxCodPrice = array_values(array_filter($limits['COD'], function ($item) use ($codServiceCode, $currency) {
            if ($item['product'] === $codServiceCode && $item['currency'] === $currency) {
                return true;
            }
            return false;
        }, true));



        $totalContents = $cart->get_cart_contents_total() + $cart->get_cart_contents_tax();

        $total = $totalContents;

        $priceWithDph = $setting->getPriceWithDph();

        $shipmentCartModel->setPriceWithDph($priceWithDph);

        if (!$maxCodPrice) {
            $shipmentCartModel->setDisableCod(true);
            if ($currencySetting->getCostOrderFree() != null
                && $currencySetting->getCostOrderFree() < $total) {
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