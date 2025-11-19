<?php
// phpcs:ignoreFile WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:ignoreFile WordPress.DB.DirectDatabaseQuery.NoCaching

namespace PPLCZ;

defined("WPINC") or die();

use PPLCZ\Admin\Assets\JsTemplate;
use PPLCZ\Front\Validator\ParcelShopValidator;
use PPLCZ\Model\Model\CartModel;
use PPLCZ\Model\Model\ShipmentMethodModel;
use PPLCZ\Model\Model\ShipmentMethodSettingModel;
use PPLCZ\ModelNormalizer\CartModelDernomalizer;
use PPLCZ\Setting\MethodSetting;


class ShipmentMethod extends \WC_Shipping_Method {


    public static function methodsDescriptions()
    {
        return [
            "PRIV" => "Doprava v rámci České republiky na adresu",
            "SMAR" => "Doprava v rámci České republiky na výdejní místo",
            "SMEU" => "Doprava v rámci Polska, Německa, Slovenska na výdejní místo",
            "CONN" => "Doprava v rámci EU na adresu",
            "COPL" => "Doprava mimo EU v rámci Evropy"
        ];
    }


    public function isCOD($paymentCode)
    {
        $cod = @$this->get_instance_option("codPayment");
        return $cod === $paymentCode;
    }


    public function __construct($method_id)
    {
        parent::__construct(intval($method_id));

        $zones_shipments = wp_cache_get(pplcz_create_name("zones_shipment")) ?: [];
        if (!$zones_shipments || $method_id === (intval($method_id) . "") && !($founded = array_filter($zones_shipments, function ($item) use ($method_id) {
                return $item->instance_id == $method_id;
            }))) {
            global $wpdb;

            $result = $wpdb->get_results( $wpdb->prepare("select instance_id, zone_id, method_id from {$wpdb->prefix}woocommerce_shipping_zone_methods where method_id like %s ", pplcz_create_name("") . "%"));

            $zones_shipments = array_merge($zones_shipments, $result);
            wp_cache_delete(pplcz_create_name("zones_shipment"));
            wp_cache_add(pplcz_create_name("zones_shipment"), $zones_shipments);
        }

        if ("{$method_id}" === (intval($method_id) . "")) {
            // issue https://github.com/PPL-CZ/PPL-WooCommerce/issues/6
            if (!isset($founded) || !$founded) {
                $founded = array_filter($zones_shipments, function ($item) use ($method_id) {
                    return $item->instance_id == $method_id;
                });
            }
            $method_id = '';
            if ($founded) {
                $method_id = reset($founded)->method_id;
            }
            $this->id = $method_id;
            $pplId = str_replace(pplcz_create_name(""), "", $method_id);
        } else if ($method_id) {
            $this->id = pplcz_create_name($method_id);
            $pplId =  str_replace(pplcz_create_name(""), "", $method_id);
        } else
            throw new \Exception();


        if ($pplId) {
            $method = MethodSetting::getMethod($pplId);
            if ($method) {
                $this->title = $this->method_title = $method->getTitle();
                $this->method_description = self::methodsDescriptions()[$pplId];
            }
        }

        $this->supports = array(
            "shipping-zones",
            "instance-settings"
        );
    }

    public function get_instance_form_fields() {
        if (!$this->instance_form_fields) {
            $this->instance_form_fields = $this->get_form_fields();
            return parent::get_instance_form_fields();
        }
        return parent::get_instance_form_fields();
    }

    public function get_form_fields()
    {
        if (!$this->form_fields) {
            $this->init_form_fields();
            return parent::get_form_fields();
        }
        return parent::get_form_fields();
    }

    public function init_form_fields()
    {
        $form_fields = array(
            'title' => array(
                'title'       => esc_html__('Název dopravy', 'ppl-cz' ),
                'type'        => 'text',
                'description' => esc_html__('Název dopravy', 'ppl-cz'  ),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => esc_html__('Podrobnějsí popis', 'ppl-cz' ),
                'type'        => 'textarea',
                'description' => esc_html__('Popis dopravy', 'ppl-cz'  ),
                'default'     => '',
                'desc_tip'    => true
            ),
        );

        $zones = \WC_Shipping_Zones::get_zones();
        $currencies  = include __DIR__ . '/config/currencies.php';

        $form_fields["priceWithDph"] = array(
            'title'       => esc_html__('Cena je s DPH', 'ppl-cz' ),
            'type'        => 'checkbox',
            'default'     => '',
            'desc_tip'    => true
        );

        $id = str_replace(pplcz_create_name(""), "", $this->id);
        $method = MethodSetting::getMethod($id);

        if ($method->getParcelRequired()) {

            $form_fields["disabledParcelBox"] = array(
                "title" => esc_html__('Nepoužívat parcelboxy', 'ppl-cz'),
                'type' => 'checkbox',
                'default' => '',
                'desc_tip' => true
            );

            $form_fields["disabledAlzaBox"] = array(
                "title" => esc_html__('Nepoužívat alzaboxy', 'ppl-cz'),
                'type' => 'checkbox',
                'default' => '',
                'desc_tip' => true
            );

            $form_fields["disabledParcelShop"] = array(
                "title" => esc_html__('Nepoužívat parcelshopy', 'ppl-cz'),
                'type' => 'checkbox',
                'default' => '',
                'desc_tip' => true
            );

            $parcelCountries = pplcz_get_parcel_countries();

            $allowedCoutries = $this->get_allowed_countries();
            foreach ($parcelCountries as $countryKey => $_ )
            {
                if (!in_array($countryKey, $allowedCoutries, true))
                    unset($parcelCountries[$countryKey]);
            }

            $parcelCountries[""] = "Nevybráno";

            if (count($parcelCountries) > 2)
            {
                $form_fields['disabledParcelCountries'] = array(
                    "title" =>   esc_html__('Nepovolené země pro výdejní místa', 'ppl-cz'),
                    'type'        => 'multiselect',
                    "options" => $parcelCountries,
                    'default'     => [],
                    'desc_tip'    => true
                );
            }
        }

        foreach (array_unique(array_merge([ get_option( 'woocommerce_currency' )],array_values($currencies))) as $currency) {
            $pplcz_currency_safe = esc_html($currency);

            $form_fields["cost_allow_{$currency}"] = array(
                'title'       => esc_html__("Povolení měny", 'ppl-cz' ),
                'type'        => 'checkbox',
                'description' => esc_html__('Povolení měny', 'ppl-cz'  ),
                'default'     => '',
                'desc_tip'    => true
            );


            $form_fields["cost_order_free_{$currency}"] = array(
                'title'       => sprintf(esc_html__("Od jaké ceny bude doprava zadarmo (v %s)", 'ppl-cz' ), $pplcz_currency_safe),
                'type'        => 'price',
                'description' => esc_html__('Od jaké ceny bude doprava zadarmo, pokud není vyplněno, nebude zadarmo', 'ppl-cz'  ),
                'default'     => '',
                'desc_tip'    => true
            );

            if (in_array($currency, ['CZK', "EUR", "PLN", "HUF", "RON"])) {

                $pplcz_text_safe = esc_html__("Příplatek za dobírku", 'ppl-cz' ) . " <span class='shipment-price-original'>" . $pplcz_currency_safe ."</span>" /*<!--<span class='shipment-price-base'>(v {$basecurrency})</span>-->*/;

                $form_fields["cost_cod_fee_{$currency}"] = array(
                    'title' => $pplcz_text_safe,
                    'type' => 'price',
                    'description' => esc_html__('Příplatek za dobírku', 'ppl-cz'),
                    'default' => '',
                    'desc_tip' => true
                );

                $form_fields["cost_cod_fee_always_{$currency}"]  = array(
                    'title' =>  esc_html__("Příplatek za dobírku i v případě dopravy zdarma", 'ppl-cz' ),
                    'type' => 'checkbox',
                    'description' => esc_html__('Příplatek za dobírku bude i v případě dopravy zdarma', 'ppl-cz'),
                    'default' => '',
                    'desc_tip' => true
                );

                $form_fields["cost_order_free_cod_{$currency}"] = array(
                    'title' => sprintf(esc_html__("Od jaké ceny bude doprava zadarmo pro dobírku (v %s)", 'ppl-cz'), $pplcz_currency_safe),
                    'type' => 'price',
                    'description' => esc_html__('Od jaké ceny bude doprava zadarmo pro dobírku, v případě nevyplnění nebude zadarmo', 'ppl-cz'),
                    'default' => '',
                    'desc_tip' => true
                );
            }

            $pplcz_text_safe = esc_html__("Cena za dopravu", 'ppl-cz' ) . " <span class='shipment-price-original'>(v {$pplcz_currency_safe})</span>";

            $form_fields["cost_{$currency}"] = array(
                'title'       => $pplcz_text_safe,
                'type'        => 'price',
                'description' => esc_html__('Cena za dopravu', 'ppl-cz'  ),
                'default'     => '',
                'desc_tip'    => true
            );

            $form_fields["cost_by_weight"] = array (
                'title' => esc_html__("Cena se počítá z váhy", 'ppl-cz' ),
                'type'        => 'checkbox',
                'description' => esc_html__("Cena se počítá z váhy", 'ppl-cz'  ),
                'default'     => '',
                'desc_tip'    => true
            );

        }

        $payments = WC()->payment_gateways()->payment_gateways();

        foreach ($payments as $key => $value )
        {
            $payments[$key] = $value->get_title();
        }

        $payments[""] = "Nevybráno";


        $form_fields["disablePayments"] = array(
            'title'       => esc_html__('Platby, které nejsou povoleny', 'ppl-cz' ),
            'type'        => 'multiselect',
            'description' =>  esc_html__( 'Platby, které nejsou povoleny', 'ppl-cz'  ),
            "options" => $payments,
            'default'     => [],
            'desc_tip'    => true
        );

        $form_fields["codPayment"] = [
            'title'       => esc_html__('Platba považovaná za dobírku', 'ppl-cz' ),
            'type'        => 'select',
            'description' => esc_html__("Vyberte platbu určenou na dobírku (u teto platby je nutno řešit i nastaveni v rámci pokladny, pro jeji zobrazení)", 'ppl-cz'  ),
            "options" =>  $payments,
            'default'     => "",
            'desc_tip'    => true
        ];

        $this->form_fields = $form_fields;
    }

    public static function shipping_methods($shippings)
    {

        return array_merge($shippings, [
            pplcz_create_name("PRIV" )=> new ShipmentMethod("PRIV"),
            pplcz_create_name("SMAR") =>  new ShipmentMethod("SMAR"),
            pplcz_create_name("SMEU")=>  new ShipmentMethod("SMEU"),
            pplcz_create_name("CONN") => new ShipmentMethod("CONN"),
            pplcz_create_name("COPL") => new ShipmentMethod("COPL"),
        ]);
    }

    public function get_allowed_countries()
    {
        $instance_id = $this->instance_id;

        $all_zones = \WC_Shipping_Zones::get_zones();
        $all_zones[] = [ 'zone_id' => 0 ];
        $allowedCountriesByZone = [];

        foreach ( $all_zones as $zone_data ) {
            $zone = new \WC_Shipping_Zone( $zone_data['zone_id'] );
            // true = načíst i neaktivní metody
            $methods = $zone->get_shipping_methods( true );

            // Zjistíme, zda naše instance patří do této zóny
            if ( isset( $methods[ $instance_id ] ) ) {
                // fallback zóna = „Rest of World“
                if ( 0 === (int) $zone_data['zone_id'] ) {
                    // přidáme všechny země, které dosud nejsou v $allowedCountries
                    foreach ( WC()->countries->get_countries() as $iso => $name ) {
                        $allowedCountriesByZone[ $iso ] = true;
                    }
                } else {
                    // běžná zóna: sbíráme jen jednotlivé země
                    foreach ( $zone->get_zone_locations() as $loc ) {
                        if ( 'country' === $loc->type ) {
                            $allowedCountriesByZone[ $loc->code ] = true;
                        }
                    }
                }
            }
        }

        return array_keys($allowedCountriesByZone);
    }

    /**
     * Výpočet ceny dopravy
     */
    public function calculate_shipping($package = array())
    {
        $enabled = $this->enabled;

        if ($enabled === "no")
            return;

        $cart = wc()->cart;

        if (!$cart) {
            wc()->initialize_cart();
            $cart = wc()->cart;
        }

        $country = WC()->cart->get_customer()->get_shipping_country('');

        /**
         * @var CartModel $cartData
         */
        $cartData = pplcz_denormalize($this, CartModel::class);

        if ($cartData->getDisabledByCountry())
            return;

        if ($cartData->getDisabledByWeight())
            return;

        if ($cartData->getParcelRequired() && $cartData->getAgeRequired() && $country !== 'CZ')
            return;

        if ($cartData->getDisabledByProduct())
            return;

        if ($cartData->getDisabledByCountry())
            return;

        if ($cartData->getDisabledByRules())
            return;


        $price = $cartData->getCost();
        $dph = $cartData->getCostDPH();
        if ($dph)
        {
            $dph = [$dph->getDphId() => $dph->getValue()];
        }
        else {
            $dph= [];
        }


        $this->add_rate(array_merge([
            'label' => $this->instance_settings["title"] ?: $this->title ?: $this->method_title,
            'cost' => $price,
            "meta_data" => array_merge(pplcz_normalize($cartData), $cartData->getIsPriceWithDph() ? ['pplcz_taxes' => $dph] : []),
        ], $cartData->getIsPriceWithDph() ? ['taxes' => $dph] : []));
    }

    public static function yay_currency($data, $method, $costs, $currency) {

        if (str_starts_with($method, pplcz_create_name("")))
        {
            unset($data['cost']);
        }

        return $data;
    }

    public static function woocommerce_package_rates($rates, $package)
    {

        foreach ($rates as $key => $item)
        {
            if (strpos($item->get_id(),"pplcz_") !== false)
            {
                /**
                 * @var \WC_Shipping_Rate $item
                 */
                $metadata = $item->get_meta_data();
                $item->set_cost($metadata['cost']);
                if (isset($metadata['taxes']))
                    $item->set_taxes($metadata['taxes']);
                else if (isset($metadata['pplcz_taxes']))
                    $item->set_taxes($metadata['pplcz_taxes']);
                $rates[$key] = $item;
            }
        }

        return $rates;
    }


    public static function recalculate_fees($cart)
    {
        $rate = pplcz_get_cart_shipping_method();
        if (!$rate)
            return $cart;

        $shipmentMethod = new ShipmentMethod($rate->get_instance_id() ?: $rate->get_method_id());
        // Odseparování volání z podmínky, l. 546
        $selected_rates = \WC_Tax::get_shipping_tax_rates();
        /**
         * @var CartModel $metadata
         */
        $metadata = pplcz_denormalize($shipmentMethod, CartModel::class);

        if ($metadata->getCodFee())
        {
            WC()->cart->fees_api()->add_fee(
                array(
                    'name' => __("Příplatek za dobírku", "ppl-cz"),
                    'amount' => (float)$metadata->getCodFee(),
                    'taxable' => !!$metadata->getCodFeeDPH(),
                    'tax_class' => $metadata->getTaxableName() ?: "",
                    "yay_currency_fee_converted" => true
                )
            );

        }

        return $cart;

    }

    public static function available_payment_methods($available_gateways) {
        if ( is_admin() ) return $available_gateways;

        // Získání aktuálně zvoleného způsobu dopravy
        $session = WC()->session;
        if (!$session)
            return $available_gateways;

        $rate = pplcz_get_cart_shipping_method();
        if (!$rate)
        {
            return $available_gateways;
        }

        /**
         * @var CartModel $metadata
         */
        $metadata = pplcz_denormalize($rate->get_meta_data(), CartModel::class);
        if ($metadata->isInitialized("disablePayments") && $metadata->getDisablePayments()) {
            $disablePayments = $metadata->getDisablePayments();
            foreach ( $available_gateways as $gateway_id => $gateway ) {
                if ( in_array( $gateway_id, $disablePayments ) ) {
                    unset( $available_gateways[$gateway_id] );
                }
            }
        }

        if ($metadata->getDisableCod() && $metadata->getCodPayment()) {
            $cod = $metadata->getCodPayment();
            unset($available_gateways[$cod]);
        }

        return $available_gateways;

    }


    public function generate_settings_html( $form_fields = array(), $echo = true ) {

        $currencies  = include __DIR__ . '/config/currencies.php';
        $currencies = array_unique(array_merge([ get_option( 'woocommerce_currency' )],array_values($currencies)));
        foreach ($form_fields as $key => $value)
        {
            $keys = explode("_", $key);
            $currency = end($keys);
            if (in_array($currency, $currencies, true))
            {
                unset($form_fields[$key]);
            }
        }

        $html = parent::generate_settings_html($form_fields, $echo);

        if (!$echo)
            ob_start();

        JsTemplate::add_inline_script("pplczInitSettingShipment", "pplczshipmentsetting");

        $json = pplcz_normalize(pplcz_denormalize($this, ShipmentMethodSettingModel::class));

        if ($echo)
            $html .= "<tr><td style='padding: 0' colspan='2'><div id='pplczshipmentsetting'  data-pplczshipmentsetting='" . esc_html(wp_json_encode($json)) . "'></div></td></tr>";
        else
            echo "<tr><td style='padding: 0'  colspan='2'><div id='pplczshipmentsetting'  data-pplczshipmentsetting='" . esc_html(wp_json_encode($json)) . "'></div></td></tr>";

        if (!$echo)
            return $html . ob_get_clean();
        return "";
    }

    public function process_admin_options()
    {
        wp_cache_delete(pplcz_create_name("zones_shipment"));


        add_filter( 'woocommerce_shipping_' . $this->id . '_instance_settings_values', function ($item, $setting) {
            $key = $this->get_field_key( "weights" );
            $postdata = $this->get_post_data();
            $values = isset($postdata[$key]) ? $postdata[$key] : [];
            if (!is_array($values))
                $values = [];

            array_walk_recursive($values, function(&$item){
               if ($item === "")
                   $item = null;
                });
                if (!$values)
                    $values = [];
                add_option( $this->get_instance_option_weight_key(), $values) || update_option( $this->get_instance_option_weight_key(), $values);
                return $item;
            },10, 2);

        parent::process_admin_options();
        wp_cache_delete(pplcz_create_name("zones_shipment"));
        pplcz_set_update_setting();
    }

    public function get_instance_option_weight_key()
    {
        return $this->get_instance_option_key() . '_weight';
    }

    public static function hide_order_itemmeta($metas)
    {
        return array_merge(['taxableName', 'priceWithDph', 'isPriceWithDph', 'disablePayments', "parcelRequired", "mapEnabled", "codFee", "codPayment", "disableCod", "ageRequired", "cost", "codFee", "serviceCode", "parcelBoxEnabled", "alzaBoxEnabled", "parcelShopEnabled"], $metas );
    }


    public static function register() {
        add_filter("woocommerce_shipping_methods", [self::class, 'shipping_methods']);
        add_filter('woocommerce_hidden_order_itemmeta', [self::class, 'hide_order_itemmeta']);
        add_filter("woocommerce_available_payment_gateways", [self::class, "available_payment_methods"]);
        add_filter('woocommerce_cart_calculate_fees', [self::class, 'recalculate_fees']);


        add_filter('yay_currency_get_data_info_from_shipping_method', [self::class, 'yay_currency'], 10, 4);
        add_filter('woocommerce_package_rates', [self::class, 'woocommerce_package_rates'], 99999, 2);
    }
}