<?php
namespace PPLCZ\Admin\Cron;
defined("WPINC") or die();


use PPLCZ\Admin\CPLOperation;
use PPLCZ\Data\BatchData;
use PPLCZ\Data\LogDataStore;
use PPLCZ\Data\PackageData;
use PPLCZ\Data\ShipmentData;

class DeleteLogCron {


    public static function delete_logs()
    {
        LogDataStore::clear_logs();
        BatchData::remove_batchs();
    }

    public static function register()
    {
        add_action(pplcz_create_name('delete_logs'), [self::class, 'delete_logs']);
    }

}