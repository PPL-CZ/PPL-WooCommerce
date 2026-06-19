<?php
namespace PPLCZ\Admin\Page;
defined("WPINC") or die();


use PPLCZ\Admin\Assets\JsTemplate;
use PPLCZ\Admin\CPLOperation;
use PPLCZ\Admin\Cron\ShipmentPhaseCron;
use PPLCZ\Setting\MethodSetting;

class OptionPage {

    const SLUG =  "pplcz_options";

    public static function render()
    {
        ?>
        <div class="wp-reset-div">
            <div id="pplcz_options" ></div>
        </div>
        <?php
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
                <p>Byla nainstalována nová verze PPL pluginu. Prosím, podívejte se na <a href='". esc_html($url). "'>novinky</a>, které jsou s aktualizací spojené.</p>
            </div>";
        }

        $maps_news = get_option(pplcz_create_name("old_map_news"));
        $map = MethodSetting::getGlobalSetting()->getMap();
        if ($maps_news !== 'ok_1'
            && ($map->getAvailableOldMap() || !$map->getEnabled() || !$map->getApikey())
        )
        {
            ob_start();
            $url = menu_page_url(self::SLUG) . '#/setting';
            ob_clean();

            $nonce = wp_create_nonce("hide_notice");
            JsTemplate::add_inline_script("pplczNotices");

            if (!$map->getAvailableOldMap()):
                echo "<div data-nonce='". esc_html($nonce)  ."' class=\"pplcz-oldmap-notice notice notice-warning is-dismissible\">
                    <p>Bez platného API klíče není možné mapu výdejních míst používat. Před integrací se ujistěte, že máte API klíč správně vytvořený a aktivní. Bližší info na stránce <a href='". esc_html($url). "'>nastavení</a> v sekci \"obecné nastavení\"</p>
                </div>";
            else:
                echo "<div data-nonce='". esc_html($nonce)  ."' class=\"pplcz-oldmap-notice notice notice-error is-dismissible\">
                    <p>
                    Podpora původních map pro výdejní místa PPL.cz bude ukončena k <b>31. 7. 2026.</b> Po tomto datu bude pro zobrazení mapy výdejních míst vyžadován platný API klíč. Před integrací se ujistěte, že je váš API klíč správně vytvořený a aktivní. Více informací naleznete v nastavení v sekci <a href='". esc_html($url). "'>„Nastavení“</a>.
                    </p>
                </div>";
            endif;
        }

    }

    public static function hide_new_notices() {
        $version = get_option(pplcz_create_name("version"));
        $code = pplcz_create_name("version_news");
        add_option($code, $version) || update_option($code, $version);
        if (!isset($_POST['hide_notice']) || !wp_verify_nonce(sanitize_key($_POST['hide_notice']), 'hide_notice'))
        {
            http_response_code(403);
            wp_die();
        }
        http_response_code(204);
        wp_die();
    }

    public static function hide_oldmap_notices() {
        $code = get_option(pplcz_create_name("old_map_news"));

        add_option($code, 'ok_1') || update_option($code, 'ok_1');
        if (!isset($_POST['hide_notice']) || !wp_verify_nonce(sanitize_key($_POST['hide_notice']), 'hide_notice'))
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
            set_transient(pplcz_create_name("validate_cpl_connect"), $validated, 3600 * 3);
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

    public static function createUrl($local_batch_id )
    {
        $vars = array_map('urlencode', array_filter(array(
                "page" => self::SLUG,
        )));

        $url = add_query_arg(
                $vars,
                admin_url('admin.php')
        );

        return $url . '#/batch/' . $local_batch_id;
    }

    public static function register()
    {
        add_action("admin_menu", [self::class, "add_menu"]);
        add_action("admin_notices", [self::class, "validate_cpl"]);

        add_action("admin_notices", [self::class, "news"]);
        add_action('wp_ajax_pplcz_hide_new_notice', [self::class, "hide_new_notices"]);
        add_action('wp_ajax_pplcz_hide_oldmap_notices',  [self::class, "hide_oldmap_notices"]);
    }
}