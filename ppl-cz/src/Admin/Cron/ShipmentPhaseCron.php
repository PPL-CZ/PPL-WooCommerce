<?php
namespace PPLCZ\Admin\Cron;
defined("WPINC") or die();


use PPLCZ\Admin\CPLOperation;
use PPLCZ\Data\PackageData;
use PPLCZ\Data\ShipmentData;
use PPLCZ\Setting\MethodSetting;
use PPLCZ\Setting\PhaseSetting;

class ShipmentPhaseCron {


    public static function refresh_shipments()
    {

        $phases = array_map(function ($item) {
            return $item->getCode();
        }, array_filter(PhaseSetting::getPhases()->getPhases(), function ($item) {
            return $item->getWatch();
        }));

        if (in_array('Canceled', $phases, true))
        {
            $phases[] = 'Deleted';
        }

        $from = (new \DateTime())->sub(new \DateInterval("PT120M"));
        $lastUpdate = (new \DateTime())->sub(new \DateInterval("P16D"));
        $max = PhaseSetting::getPhases()->getMaxSync();

        $packages = PackageData::find_packages_by_phases_and_time(array_merge($phases, ['None']), $from->format("Y-m-d H:i:s"), $lastUpdate->format("Y-m-d H:i:s"), $max+1);
        $next = $max < count($packages);

        if ($packages) {
            $operation = new CPLOperation();
            if ($operation->getAccessToken()) {
                $packages = array_values($packages);
                $update = array_slice($packages, 0, 40);
                if ($update) {
                    $operation->testPackageStates(array_map(function ($item) {
                        return $item->get_id();
                    }, $update));
                }
            }
        }

        $items = ShipmentData::read_progress_shipments();
        if ($items) {
            $operation = new CPLOperation();
            $operation->loadingShipmentNumbers(array_map(function ($item) {
                return $item->get_batch_id();
            }, $items));
        }

        if ($next) {
            as_schedule_single_action(time() + 60, pplcz_create_name("refresh_shipments_cron"));
        }

    }

    public static function register()
    {
        add_action(pplcz_create_name('refresh_shipments_cron'), [self::class, 'refresh_shipments']);
    }

}