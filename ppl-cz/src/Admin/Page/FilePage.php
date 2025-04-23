<?php
namespace PPLCZ\Admin\Page;

defined("WPINC") or die();


use PPLCZ\Data\PackageData;
use PPLCZ\Data\ShipmentData;
use WpOrg\Requests\Exception;

class FilePage {

    const SLUG = "pplcz_filedownload";

    public static function render() {
?>
        I am teapot!
<?php
    }

    public static function createUrl($download, $reference = null, $print = null )
    {
        $vars = array_map('urlencode', array_filter(array(
            "page" => self::SLUG,
            "pplcz_download" => $download,
            "pplcz_reference" => $reference,
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
            "pplcz_download" => null,
            "pplcz_reference" => null,
            "pplcz_print" => null
        );

        if (isset($_GET['pplcz_download']))
            $vars['pplcz_download'] = wp_strip_all_tags($_GET['pplcz_download']);
        if (isset($_GET['pplcz_reference']))
            $vars['pplcz_reference'] = wp_strip_all_tags($_GET['pplcz_reference']);
        if (isset($_GET['pplcz_print']))
            $vars['pplcz_print'] = wp_strip_all_tags($_GET['pplcz_print']);

        if (isset($vars['pplcz_download']) && $vars['pplcz_download']) {

            $shipmentId = $vars['pplcz_download'];
            try {
                if (is_numeric($shipmentId)) {
                    $downloadLabel = new \PPLCZ\Admin\CPLOperation();
                    $packageData = new PackageData($shipmentId);
                    if (!$packageData->get_id())
                        wp_die(esc_html__('Soubor nebyl nalezen.', 'ppl-cz'));
                    $shipmentId = $packageData->get_ppl_shipment_id();
                    $shipmentData = new ShipmentData($shipmentId);
                    $downloadLabel->getLabelContents($shipmentData->get_batch_id(), $shipmentData->get_reference_id(), $packageData->get_shipment_number(), $vars['pplcz_print']);
                } else if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $shipmentId)) {
                    $reference = $vars['pplcz_reference'];
                    $finded = ShipmentData::read_shipments(["batch_label_group" => [$shipmentId]]);
                    if ($finded) {
                        $batch_id = $finded[0]->get_batch_id();
                        $operation = new \PPLCZ\Admin\CPLOperation();
                        $operation->getLabelContents($batch_id, $reference, null, $vars['pplcz_print']);
                    }
                }
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
        $hook = add_submenu_page(null, "File download", "File download", "manage_woocommerce", self::SLUG, [self::class, "render"]);
        add_action("load-{$hook}", [self::class, "page_hook"]);
    }

    public static function register()
    {
        add_action("admin_menu", [self::class, "add_menu"]);
    }

}