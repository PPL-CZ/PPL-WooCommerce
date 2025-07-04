<?php
namespace PPLCZ\Admin\Page;
defined("WPINC") or die();


use PPLCZ\Admin\Assets\JsTemplate;
use PPLCZ\Admin\CPLOperation;
use PPLCZ\Admin\Cron\ShipmentPhaseCron;

class OptionPage {

    const SLUG =  "pplcz_options";

    public static function render()
    {
        ?>
        <div class="wp-reset-div">
            <div id="pplcz_options" ></div>
        </div>
        <?php
        JsTemplate::add_inline_script("wpUpdateStyle", "pplcz_options");
        JsTemplate::add_inline_script("optionsPage", "pplcz_options");
    }

    public static function page_hook()
    {
        JsTemplate::scripts();
    }

    public static function add_menu()
    {
        static $addMenu;
        if ($addMenu)
            return;
        $addMenu = true;
        $hook = add_menu_page("PPL CZ - Plugin", "PPL CZ - Plugin", "manage_woocommerce", self::SLUG, [self::class, "render"], pplcz_asset_icon("pplmenu.png"));
        add_action("load-{$hook}", [self::class, "page_hook"]);
    }

    public static function news()
    {
        $version = get_option(pplcz_create_name("version"));
        $version_news = get_option(pplcz_create_name("version_news"));

        if ($version != $version_news)
        {
            ob_start();
            $url = menu_page_url(self::SLUG) . '#/news';
            ob_clean();

            $nonce = wp_create_nonce("hide_notice");
            JsTemplate::add_inline_script("pplczNotices");
            echo "<div data-nonce='". esc_html($nonce)  ."' class=\"pplcz-news-notice notice notice-info is-dismissible\">
                <p>Byla nainstalována nová verze ppl plugin. Prosím, podívejte se na <a href='". esc_html($url). "'>novinky</a>, které jsou s aktualizací spojené.</p>
            </div>";
        }
    }

    public static function hide_new_notices() {
        $version = get_option(pplcz_create_name("version"));
        $code = pplcz_create_name("version_news");
        add_option($code, $version) || update_option($code, $version);
        if (!wp_verify_nonce(sanitize_key($_POST['pplNonce']), 'hide_notice'))
        {
            http_response_code(403);
            wp_die();
        }
        http_response_code(204);
        wp_die();
    }

    public static function  validate_cpl() {
        $validated = get_transient(pplcz_create_name("validate_cpl_connect"));
        $validateCallApi = get_transient(pplcz_create_name("validate_cpl_connect_api"));

        if (!$validated ) {
            delete_transient(pplcz_create_name("validate_cpl_connect_api"));
            $validateCallApi = null;
            $cpl = new CPLOperation();
            $cpl->clearAccessToken();
            $newToken = $cpl->getAccessToken();
            $validated = !$newToken ? -1: 1;
            set_transient(pplcz_create_name("validate_cpl_connect"), $validated, 3600);
        }
        self::add_menu();
        ob_start();
        $url = menu_page_url(self::SLUG) . '#/setting';
        ob_clean();
        if ($validated == -1) {
            ?>
            <div class="notice notice-error is-dismissible">
                <p>
                    PPL Plugin nemůže fungovat, protože <a href='<?php echo esc_html($url)?>'>přihlašovací údaje</a> nejsou správně nastaveny! Ujistěte se, že jsou zadány správně.<br/>
                    Pokud přístupové údaje nemáte, prosím kontaktujte ithelp@ppl.cz
                </p>
            </div>
            <?php
        }

        if ($validated == 1 && !$validateCallApi)
        {
            try {
                $cpl = new CPLOperation();
                if ($cpl->getAccessToken()) {
                    $cpl->getStatuses();
                    set_transient(pplcz_create_name("validate_cpl_connect_api"), "valid");
                    $validateCallApi = "valid";
                }
            }
            catch (\Exception $exception)
            {
                set_transient(pplcz_create_name("validate_cpl_connect_api"), $exception->getMessage());
                $validateCallApi = $exception->getMessage();
            }
        }
        if ($validateCallApi && $validateCallApi !== "valid")
        {
            ?>
            <div class="notice notice-error is-dismissible">
                <p>Problém s připojením na API: <?php echo esc_html($validateCallApi) ?></p>
            </div>
            <?php
        }
    }

    public static function register()
    {
        add_action("admin_menu", [self::class, "add_menu"]);
        add_action("admin_notices", [self::class, "validate_cpl"]);

        add_action("admin_notices", [self::class, "news"]);
        add_action('wp_ajax_pplcz_hide_new_notice', [self::class, "hide_new_notices"]);
    }
}