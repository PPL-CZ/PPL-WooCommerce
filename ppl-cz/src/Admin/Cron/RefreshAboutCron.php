<?php
namespace PPLCZ\Admin\Cron;

class RefreshAboutCron {

    public static function refresh_setting()
    {
        define("PPLCZ_REFRESH", 1);
        require_once __DIR__ . '/../../config/cod_currencies.php';
        require_once __DIR__ . '/../../config/countries.php';
        require_once __DIR__ . '/../../config/currencies.php';
        require_once __DIR__ . '/../../config/limits.php';
        require_once __DIR__ . '/../../config/shipment_phases.php';
        require_once __DIR__ . '/../../config/statuses.php';
        require_once __DIR__ . '/../../config/collection_address.php';

    }

    public static function register()
    {
        add_action(pplcz_create_name("refresh_setting_cron"), [self::class, 'refresh_setting']);
    }
}