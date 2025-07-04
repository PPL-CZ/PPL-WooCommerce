<?php
namespace PPLCZ\Front\Components\ParcelShopSummary;

defined("WPINC") or die();


use Automattic\WooCommerce\Blocks\Assets\Api as AssetApi;
use Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry;
use Automattic\WooCommerce\Blocks\BlockTypes\OrderConfirmation\AbstractOrderConfirmationBlock;
use Automattic\WooCommerce\Blocks\BlockTypes\OrderConfirmation\ShippingAddress;

use Automattic\WooCommerce\Blocks\Domain\Services\CheckoutFields;
use Automattic\WooCommerce\Blocks\Integrations\IntegrationRegistry;
use Automattic\WooCommerce\Blocks\Package;
use PPLCZ\Traits\ParcelDataModelTrait;

class BlockOrderConfirmation extends ShippingAddress {

    use ParcelDataModelTrait;

    /**
     * Block name.
     *
     * @var string
     */
    protected $block_name = 'order-confirmation-parcelshop-shipping-address';
    protected $namespace = 'pplcz';

    protected function render( $attributes, $content, $block ) {
        $className = "";
        if (isset($attributes['className']))
            $className = $attributes['className'] ?: "";

        $attributes["className"] = trim(($className) . " wc-block-order-confirmation-shipping-address");
        /**
         * @var \WP_Block $block
         * @var \WP_Block_Type $blockType
         */
        $block->block_type->style_handles[] = "wc-blocks-style-order-confirmation-shipping-address";

        return parent::render( $attributes, $content, $block );
    }
    protected function render_content($order, $permission = false, $attributes = [], $content = '')
    {
        /**
         * @var \WC_Order $order
         */
        if ( ! $permission || ! $order->needs_shipping_address() || ! $order->has_shipping_address() ) {
            return $this->render_content_fallback();
        }

        $shippingAddress = self::getParcelshopOrderData($order);
        ob_start();
        wc_get_template("ppl/parcelshop-shipping-address.php", ["shippingAddress" => $shippingAddress] );
        return ob_get_clean();

    }


    public static function test_order () {
        $container = Package::container();
        new BlockOrderConfirmation( $container->get( AssetApi::class ), $container->get( AssetDataRegistry::class ), new IntegrationRegistry() );
        $order_id = absint( get_query_var( 'order-received' ) );
        if($order_id) {
            $order = wc_get_order( $order_id );
            if (self::getParcelshopOrderData($order, true)) {
                add_filter('render_block_data', [self::class, "update_block"], 100, 3);
            }
        }
    }

    private static function walk_in_block(array $parsed_block)
    {
        if ($parsed_block['blockName'] === "woocommerce/order-confirmation-shipping-address")
        {
            $parsed_block["blockName"] = "pplcz/order-confirmation-parcelshop-shipping-address";
        }
        else
        {
            if ($parsed_block["innerBlocks"])
            {
                foreach ($parsed_block["innerBlocks"] as $key => $block)
                {
                    $parsed_block["innerBlocks"][$key] = self::walk_in_block($block);
                }
            }
        }
        return $parsed_block;
    }

    public static function update_block($parsed_block, $source_block, $parent_block ) {
        return self::walk_in_block($parsed_block);
    }

    public static function register() {
        add_action("wp", [self::class, "test_order"]);
    }
}