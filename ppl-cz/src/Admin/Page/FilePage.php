<?php
namespace PPLCZ\Admin\Page;

defined("WPINC") or die();


use PPLCZ\Data\PackageData;
use PPLCZ\Data\ShipmentData;
use WpOrg\Requests\Exception;

class FilePage {

    const SLUG = "pplcz_filedownload";

    public static function render() {
        throw new \Exception("Not implemented");
?>
        I am teapot!
<?php
    }

    public static function createUrl($remote_batch, $shipment = null, $package = null, $print = null )
    {
        $vars = array_map('urlencode', array_filter(array(
            "page" => self::SLUG,
            "pplcz_remote_batch" => $remote_batch,
            "pplcz_shipment" => $shipment,
            "pplcz_package" => $package,
            "pplcz_print" => $print
        )));

        $url = add_query_arg(
            $vars,
            admin_url('admin.php')
        );
        return $url;
    }

    public static function page_hook()
    {
        $vars = array(
            "pplcz_remote_batch"=> null,
            "pplcz_shipment" => null,
            "pplcz_package" => null,
            "pplcz_print" => null
        );

        if (isset($_GET['pplcz_remote_batch']))
            $vars['pplcz_remote_batch'] = wp_strip_all_tags($_GET['pplcz_remote_batch']);
        if (isset($_GET['pplcz_shipment']))
            $vars['pplcz_shipment'] = wp_strip_all_tags($_GET['pplcz_shipment']);
        if (isset($_GET['pplcz_package']))
            $vars['pplcz_package'] = wp_strip_all_tags($_GET['pplcz_package']);

        if (isset($_GET['pplcz_print']))
            $vars['pplcz_print'] = wp_strip_all_tags($_GET['pplcz_print']);

        if (isset($vars['pplcz_remote_batch']) && $vars['pplcz_remote_batch']) {
            $downloadLabel = new \PPLCZ\Admin\CPLOperation();
            try {
                $downloadLabel->getLabelContents($vars['pplcz_remote_batch'], $vars['pplcz_shipment'],  $vars['pplcz_package'], $vars['pplcz_print']);
            }
            catch (\Exception $exception)
            {
                wp_die(esc_html(__('Problém se stažením, nepokoušíte se tisknout štítky starší než 31 dní?', "ppl-cz")));
            }
            wp_die(esc_html(__('Soubor nebyl nalezen', "ppl-cz")));
        }

        return $vars;
    }

    public static function add_menu()
    {
        static $addMenu;
        if ($addMenu)
            return;
        $addMenu = true;
        $hook = add_submenu_page("", "File download", "File download", "manage_woocommerce", self::SLUG, [self::class, "render"]);
        add_action("load-{$hook}", [self::class, "page_hook"]);
    }

    public static function register()
    {
        add_action("admin_menu", [self::class, "add_menu"]);
    }

}