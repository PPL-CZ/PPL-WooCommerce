<?php
namespace PPLCZ\Front\Components\Whisperer;

defined("WPINC") or die();


use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

define('PPLCZ_WHISPERER_BLOCK_VERSION', '1.0.0');

class Block implements IntegrationInterface
{

    /**
     * The name of the integration.
     *
     * @return string
     */
    public function get_name()
    {
        return 'WhispererBlock';
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
        return array('whisperer-block-frontend');
    }

    /**
     * Returns an array of script handles to enqueue in the editor context.
     *
     * @return string[]
     */
    public function get_editor_script_handles()
    {
        return array('whisperer-block-editor');
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
        $script_path = '/Whisperer/build/index.js';
        $script_url = plugins_url( $script_path, __DIR__);
        $script_asset_path = __DIR__ . '/build/index.asset.php';
        $script_asset = file_exists($script_asset_path)
            ? require $script_asset_path
            : array(
                'dependencies' => array(),
                'version' => $this->get_file_version($script_asset_path),
            );


        wp_register_script(
    'whisperer-block-editor',
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
        $script_path = '/Whisperer/build/whisperer-shop-frontend.js';
        $script_url = plugins_url($script_path, __DIR__);
        $script_asset_path = __DIR__ . '/build/whisperer-shop-frontend.asset.php';

        $script_asset = file_exists($script_asset_path)
            ? require $script_asset_path
            : array(
                'dependencies' => array(),
                'version' => $this->get_file_version($script_asset_path),
            );



        wp_register_script(
            'whisperer-block-frontend',
            $script_url,
            array_merge($script_asset['dependencies']),
            $script_asset['version'],
            true
        );

        wp_localize_script("whisperer-block-frontend", "whisperer_block_frontend", [
            "assets_url" => plugins_url('Admin/Assets/Images', realpath(__DIR__ .'/../../') ),

        ]);
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
        return PPLCZ_WHISPERER_BLOCK_VERSION;
    }

}