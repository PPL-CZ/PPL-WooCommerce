<?php
namespace PPLCZ\Front\Components\Map;

use PPLCZ\Setting\MethodSetting;

defined("WPINC") or die();

class Map {

    public static function register() {

        add_action("init", [self::class, 'rewrite_rule']);
        add_action('init', [self::class, 'wp_register']);
        add_filter('request', [self::class, 'request'], 100, 1);
        add_filter('query_vars', [self::class, 'query_vars']);
        add_filter("template_include", [self::class, 'template_include'], 8000);
    }

    public static function rewrite_rule()
    {
        add_rewrite_rule("^ppl-map(/.+)?", "index.php?ppl_map=1", "top");
    }

    public static function query_vars($vars) {
        return array_merge($vars, [
            "ppl_map",
            "ppl_lat",
            "ppl_lng",
            "ppl_withCard",
            "ppl_withCash",
            "ppl_country",
            "ppl_parcelshop",
            "ppl_parcelbox",
            "ppl_address",
            "ppl_hiddenpoints",
            "ppl_countries"
        ]);
    }

    public static function request($vars)
    {
        if (isset($vars['ppl_map']) && $vars['ppl_map'] === "1") {
            $vars['ppl_map'] = true;
            $vars['ppl_lat'] = isset ($vars['ppl_lat']) ? floatval($vars['ppl_lat'] ?: "0") : 0;
            $vars['ppl_lng'] = isset($vars['ppl_lng']) ? floatval($vars['ppl_lng'] ?: "0") : 0;
            $vars['ppl_withCard'] = isset($vars['ppl_withCard']) ? intval($vars['ppl_withCard'] ?: "0") : 0;
            $vars['ppl_withCash'] = isset($vars['ppl_withCash']) ? intval($vars['ppl_withCash'] ?: "0") : 0;
            $vars['ppl_parcelbox'] = isset($vars['ppl_parcelbox']) ? intval($vars['ppl_parcelbox'] ?: "0") : 0;
            $vars['ppl_parcelshop'] = isset($vars['ppl_parcelshop']) ? intval($vars['ppl_parcelshop'] ?: "0") : 0;
            $vars['ppl_address'] = isset($vars['ppl_address']) ? ($vars['ppl_address'] ?: null) : null;
            $vars['ppl_country'] = isset($vars['ppl_country']) && $vars['ppl_country'] ? strtolower($vars['ppl_country']) : null;
            $vars['ppl_hiddenpoints'] = isset($vars['ppl_hiddenpoints']) ? ($vars['ppl_hiddenpoints'] ?: null) : null;
            $vars['ppl_countries'] = isset($vars['ppl_countries']) ? (@$vars['ppl_countries'] ?: null) : null;
        }
        return $vars;
    }

    private static function oldmap() {
        global $wp_query;
        $lat = $wp_query->query_vars['ppl_lat'];
        $lng = $wp_query->query_vars['ppl_lng'];
        $withCard = $wp_query->query_vars['ppl_withCard'];
        $withCash = $wp_query->query_vars['ppl_withCash'];
        $country = $wp_query->query_vars['ppl_country'];
        $hiddenPoints = $wp_query->query_vars['ppl_hiddenpoints'];
        $countries = $wp_query->query_vars['ppl_countries'];

        $address = $wp_query->query_vars['ppl_address'];
        $data = [];

        if (floatval($lat) && floatval($lng)) {
            $data["data-lat"] = $lat;
            $data["data-lng"] = $lng;
        }
        $data['data-initialfilters'] = [];
        if (intval($withCard)) {
            $data["data-initialfilters"][] = "CardPayment";
        }
        if (intval($withCash))
            $data["data-initialfilters"][] = "ParcelShop";

        if (!$data["data-initialfilters"]) {
            unset($data["data-initialfilters"]);
        } else {
            $data["data-initialfilters"] = join(',', $data["data-initialfilters"]);
        }
        if (isset($data["data-lat"]) && $data['data-lat']) {

            $data["data-mode"] = "static";
        }
        if ($hiddenPoints)
            $data['data-hiddenpoints'] = $hiddenPoints;

        if ($countries)
            $data['data-countries'] = $countries;

        if ($address)
        {
            $data["data-address"] = $address;
        }

        if ($country)
        {
            $data['data-country'] = $country;
        }

        $languageMap = pplcz_create_name("map_language");
        $lang = strtolower(get_option($languageMap));
        if (!in_array($lang, ["cs", "en"]))
            $lang = 'cs';

        $data['data-language'] = $lang;

        return $data;
    }

    private static function newmap() {
        $map  = MethodSetting::getGlobalSetting()->getMap();

        global $wp_query;
        $lat = $wp_query->query_vars['ppl_lat'];
        $lng = $wp_query->query_vars['ppl_lng'];
        $withCard = $wp_query->query_vars['ppl_withCard'];
        $withCash = $wp_query->query_vars['ppl_withCash'];
        $country = $wp_query->query_vars['ppl_country'];
        $hiddenPoints = array_filter(explode(',', $wp_query->query_vars['ppl_hiddenpoints'] ?: ''));
        $allowedAccessPoint = array_diff(['AlzaBox', 'ParcelBox', 'ParcelShop'], $hiddenPoints);
        $countries = $wp_query->query_vars['ppl_countries'];

        $address = $wp_query->query_vars['ppl_address'];
        $languageMap = pplcz_create_name("map_language");
        $lang = strtolower(get_option($languageMap));
        switch($lang)
        {
            case 'cs':
            case 'en':
                break;
            default:
                $lang = 'cs';
        }

        $data = [
            "mode" => "SHOPCART",
            'allowedAccessPointTypes' => $allowedAccessPoint,
            'allowedCountries' => array_filter([strtoupper($country)]),
            'defaultLanguage' => $lang,
            'viewMode' => "inline",
            "defaultCountry" => strtoupper($country),
            "disabledAccessPointTypes" => $hiddenPoints
        ];

        if ($address)
            $data['centeredToAddress'] = $address;

        $apikey =  $map->getApikey();

        if (floatval($lat) && floatval($lng)) {
            $data['centeredToLat'] = $lat;
            $data['centeredToLon'] = $lng;
        }

        if ($withCash) {
            $data['codRequired'] = true;
        }

        return ['apikey' => $apikey, 'config' => $data];
    }

    public static function args()
    {
        $map  = MethodSetting::getGlobalSetting()->getMap();
        if ($map->getEnabled()) {
            return self::newmap();
        }
        else {
            return self::oldmap();
        }
    }

    public static function wp_register()
    {

        $path = plugins_url("/Map/ppl-map.css", __DIR__);
        wp_styles()->add( "pplcz_map_css", $path, [], pplcz_get_version(), "all" );
        $path = plugins_url("/Map/ppl-map.js", __DIR__);
        wp_scripts()->add( "pplcz_map_js", $path, ["jquery"], pplcz_get_version() );
        $mapUrl = rtrim(site_url(), '/') . '/ppl-map';
        global $wp_rewrite;
        if (!$wp_rewrite->using_permalinks())
            $mapUrl = rtrim(site_url()) . '?ppl_map=1';
        wp_scripts()->localize('pplcz_map_js', 'PPLCZ_MAP_JS', array('siteurl' => get_option('siteurl'), 'mapurl' => $mapUrl) );

    }

    public static function wp_enqueue()
    {
        $map  = MethodSetting::getGlobalSetting()->getMap();

        if ($map->getEnabled()) {
           wp_enqueue_script("ppl_external_js", "https://ppl.cz/accesspointwidget/loader.js", [], pplcz_get_version(), true); //  updating is different from plugins, cannot be done locally
        }
        else {
            wp_enqueue_style("ppl_external_css", "https://www.ppl.cz/sources/map/main.css", [], pplcz_get_version()); //  updating is different from plugins, cannot be done locally
            wp_enqueue_script("ppl_external_js",  "https://www.ppl.cz/sources/map/main.js", [], pplcz_get_version(), true); //  updating is different from plugins, cannot be done locally
        }

        $path = plugins_url("/Map/ppl-external.css", __DIR__);
        wp_enqueue_style("ppl_internal_css", $path, [], pplcz_get_version());
        $path = plugins_url("/Map/ppl-external.js", __DIR__);
        wp_enqueue_script("ppl_internal_js", $path, [], pplcz_get_version(), true);

    }

    public static function template_include($template)
    {

        $map  = MethodSetting::getGlobalSetting()->getMap();
        $mappath = "/ppl/parcelshops-map.php";
        if ($map->getEnabled())
        {
            $mappath = "/ppl/parcelshops-new-map.php";
        } else if (!$map->getAvailableOldMap())
        {
            $mappath = "/ppl/parcelshops-disabled-map.php";
        }

        global $wp_query;
        if (isset($wp_query->query_vars['ppl_map']) && $wp_query->query_vars['ppl_map']) {
            self::wp_enqueue();
            $path1 = get_stylesheet_directory();
            $path2 = get_template_directory();
            if (file_exists($path1 . $mappath))
                return $path1 . $mappath;
            else if ($path2 !== $path1 && file_exists($path2 . $mappath))
                return $path2 . $mappath;
            return __DIR__ . '/../../../Template' . $mappath;
        }
        return $template;
    }

}