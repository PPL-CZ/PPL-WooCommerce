<?php
namespace PPLCZ\Front\Components\ParcelShopSummary;

defined("WPINC") or die();


use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

define('PPLCZ_PARCEL_SHOP_SUMMARY', '1.0.0');

class Block implements IntegrationInterface
{

    /**
     * The name of the integration.
     *
     * @return string
     */
    public function get_name()
    {
        return 'ParcelShopSummaryBlock';
    }

    /**
     * When called invokes any initialization/setup for the integration.
     */
    public function initialize()
    {
        $this->register_block_frontend_scripts();
        $this->register_block_editor_scripts();



    }

    /**
     * Returns an array of script handles to enqueue in the frontend context.
     *
     * @return string[]
     */
    public function get_script_handles()
    {
        wp_enqueue_style("parcelshop-block-summary-css");
        return array('parcelshop-summary-block-frontend');
    }

    /**
     * Returns an array of script handles to enqueue in the editor context.
     *
     * @return string[]
     */
    public function get_editor_script_handles()
    {
        wp_enqueue_style("parcelshop-block-editor-summary-css");
        return array('parcelshop-summary-block-editor');
    }

    /**
     * An array of key, value pairs of data made available to the block on the client side.
     *
     * @return array
     */
    public function get_script_data()
    {
        return array();
    }

    /**
     * Register scripts for delivery date block editor.
     *
     * @return void
     */
    public function register_block_editor_scripts()
    {
        $script_path = '/ParcelShopSummary/build/index.js';
        $script_url = plugins_url( $script_path, __DIR__);
        $script_asset_path = __DIR__ . '/build/index.asset.php';
        $script_asset = file_exists($script_asset_path)
            ? require $script_asset_path
            : array(
                'dependencies' => array(),
                'version' => $this->get_file_version($script_asset_path),
            );


        $css_url = plugins_url( $script_path, __DIR__);


        wp_register_style("parcelshop-block-editor-summary-css", $css_url, [], $script_asset['version']);

        wp_register_script(
    'parcelshop-summary-block-editor',
            $script_url,
            $script_asset['dependencies'],
            $script_asset['version'],
       true
        );

    }

    /**
     * Register scripts for frontend block.
     *
     * @return void
     */
    public function register_block_frontend_scripts()
    {
        $script_path = '/ParcelShopSummary/build/parcel-shop-frontend.js';
        $script_url = plugins_url($script_path, __DIR__);
        $script_asset_path = __DIR__ . '/build/parcel-shop-frontend.asset.php';

        $script_asset = file_exists($script_asset_path)
            ? require $script_asset_path
            : array(
                'dependencies' => array(),
                'version' => $this->get_file_version($script_asset_path),
            );

        $script_css = '/ParcelShopSummary/build/parcel-shop-frontend.css';
        $script_css = plugins_url($script_css, __DIR__);

        wp_register_style("parcelshop-block-summary-css", $script_css, [],  $script_asset['version']);
        wp_register_script(
            'parcelshop-summary-block-frontend',
            $script_url,
            array_merge($script_asset['dependencies'], ['pplcz_map_js']),
            $script_asset['version'],
            true
        );

    }

    /**
     * Get the file modified time as a cache buster if we're in dev mode.
     *
     * @param string $file Local path to the file.
     * @return string The cache buster value to use for the given file.
     */
    protected function get_file_version($file)
    {
        if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG && file_exists($file)) {
            return filemtime($file);
        }
        return PPLCZ_PARCEL_SHOP_SUMMARY;
    }

}