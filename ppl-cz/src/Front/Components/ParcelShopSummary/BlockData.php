<?php
namespace PPLCZ\Front\Components\ParcelShopSummary;

defined("WPINC") or die();


class BlockData {

    public static function block_registration( $integration_registry ) {
        $integration_registry->register( new Block() );
    }

    public static function block_loaded() {
        add_action('woocommerce_blocks_checkout_block_registration', [self::class, 'block_registration']);
        add_action('woocommerce_blocks_cart_block_registration', [self::class, 'block_registration']);

        woocommerce_store_api_register_update_callback(
            array (
                'namespace'       =>  pplcz_create_name('refresh_payment'),
                'callback'   => [self::class, 'update_callback'],
            )
        );
    }

    public static function update_callback($data)
    {
        if (!WC()->session)
            WC()->initialize_session();
        if (is_array($data))
            $data = reset($data);

        WC()->session->set("chosen_payment_method", $data);
        return;
    }


    public static function  register()
    {
        add_action("woocommerce_blocks_loaded", [self::class, 'block_loaded']);
    }
}
