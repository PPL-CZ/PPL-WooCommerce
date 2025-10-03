<?php
// phpcs:ignoreFile WordPress.Security.EscapeOutput.OutputNotEscaped

namespace PPLCZ\Front\Components\ParcelShop;

defined("WPINC") or die();

use PPLCZ\Front\Assets\JsFrontTemplate;
use PPLCZ\Model\Model\ParcelDataModel;
use PPLCZ\Model\Normalizer\ParcelDataModelNormalizer;
use PPLCZ\Serializer;
use PPLCZ\Traits\ParcelDataModelTrait;

class BlockData {
    use ParcelDataModelTrait;

    public static function block_registration( $integration_registry ) {
        $integration_registry->register( new Block() );
    }

    public static function cart_block_registration($integration_registry)
    {
        $integration_registry->register( new Block() );
    }

    public static function cb_data_callback() {

        $parcelshopData = pplcz_get_cart_parceldata();
        if ($parcelshopData) {
            $data = Serializer::getInstance()->normalize($parcelshopData);
            if ($data)
                return ["parcel-shop" => $data];
        }
        return ["parcel-shop" => null];
    }

    public static function cb_schema_callback() {
        $decoded = wp_json_file_decode(__DIR__ . '/../../../schema.json',  ["associative"=>true]);
        $properties = $decoded["components"]["schemas"]["ParcelDataModel"];
        return $properties;
    }

    public static function update_callback($data) {
        if (isset($data)) {
            if ($data['parcel-shop']) {
                pplcz_set_cart_parceldata(Serializer::getInstance()->denormalize($data["parcel-shop"], ParcelDataModel::class));
            } else {
                pplcz_set_cart_parceldata(null);
            }
        }
    }

    public static function block_loaded() {
        add_action('woocommerce_blocks_checkout_block_registration', [self::class, 'block_registration']);
        add_action('woocommerce_blocks_cart_block_registration', [self::class, 'cart_block_registration']);

        woocommerce_store_api_register_endpoint_data(
            array(
                'endpoint'        => \Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema::IDENTIFIER,
                'namespace'       => pplcz_create_name('parcelshop'),
                'data_callback'   => [self::class, 'cb_data_callback'],
                'schema_callback' => [self::class, 'cb_schema_callback'],
                'schema_type'     => ARRAY_A,
            )
        );
        woocommerce_store_api_register_update_callback(
            array (
                'namespace'       =>  pplcz_create_name('parcelshop'),
                'callback'   => [self::class, 'update_callback'],
            )
        );
    }

    public static function wp_head() {
        $isCart_safe = wp_json_encode(!!is_cart()) ? "1" : "0";

?><meta property="pplcz:cart" content="<?php echo ($isCart_safe ? "1" : "0")  ?>" ><?php
    }

    public static function  register()
    {
        add_action("woocommerce_blocks_loaded", [self::class, 'block_loaded']);
        add_action("wp_head", [self::class, "wp_head"]);
    }
}
