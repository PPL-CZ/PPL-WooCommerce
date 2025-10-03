<?php
namespace PPLCZ\Front\Components\Whisperer;

defined("WPINC") or die();


use PPLCZ\Admin\CPLOperation;
use PPLCZ\Model\Model\WhisperAddressModel;
use PPLCZ\Model\Model\WhisperSettingModel;
use PPLCZ\Serializer;

class BlockData {


    public static function block_registration( $integration_registry ) {
        $integration_registry->register( new Block() );
    }

    public static function cb_data_callback() {

        $model = new WhisperSettingModel();
        $client_id = get_option(pplcz_create_name("client_id"));
        $model->setActive(!!$client_id);
        $model->setUrl( rtrim(get_rest_url(), '/') . '/pplcz/v1/whisper');
        return Serializer::getInstance()->normalize($model, "array");
    }

    public static function cb_schema_callback() {
        $decoded = wp_json_file_decode(__DIR__ . '/../../../schema.json', ["associative"=>true]);
        $properties = $decoded["components"]["schemas"]["WhisperSettingModel"];
        return $properties;
    }

    public static function update_callback($data) {
        if ($data['address']) {
            /**
             * @var WhisperAddressModel $address
             */
            $address =  Serializer::getInstance()->denormalize($data['address'], WhisperAddressModel::class);
            if ($address->isInitialized("street"))
                wc()->cart->get_customer()->set_shipping_address($address->getStreet());

            wc()->cart->get_customer()->set_shipping_country("CZ");
            if ($address->isInitialized("city"))
                wc()->cart->get_customer()->set_shipping_city($address->getCity());

            if ($address->isInitialized("zipCode"))
                wc()->cart->get_customer()->set_shipping_postcode($address->getZipCode());
        }
    }

    public static function block_loaded() {
        add_action('woocommerce_blocks_checkout_block_registration', [self::class, 'block_registration']);

        woocommerce_store_api_register_endpoint_data(
            array(
                'endpoint'        => \Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema::IDENTIFIER,
                'namespace'       => pplcz_create_name('whisperer'),
                'data_callback'   => [self::class, 'cb_data_callback'],
                'schema_callback' => [self::class, 'cb_schema_callback'],
                'schema_type'     => ARRAY_A,
            )
        );
        woocommerce_store_api_register_update_callback(
            array (
                'namespace'       =>  pplcz_create_name('whisperer'),
                'callback'   => [self::class, 'update_callback'],
            )
        );
    }

    public static function  register()
    {
        add_action("woocommerce_blocks_loaded", [self::class, 'block_loaded']);
    }
}
