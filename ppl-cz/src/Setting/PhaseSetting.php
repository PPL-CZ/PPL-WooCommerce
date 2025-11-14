<?php
namespace PPLCZ\Setting;

use PPLCZ\Model\Model\SyncPhasesModel;

class PhaseSetting
{
    public static function setPhase($key, $watch, $orderState)
    {
        if ($watch || $orderState) {
            add_option(pplcz_create_name("watch_phases_{$key}"), !!$watch) || update_option(pplcz_create_name("watch_phases_{$key}"), !!$watch);
            add_option(pplcz_create_name("watch_phases_{$key}_orderState"), $orderState) || update_option(pplcz_create_name("watch_phases_{$key}_orderState"), $orderState);
        }
        else {
            delete_option(pplcz_create_name("watch_phases_{$key}"));
            delete_option(pplcz_create_name("watch_phases_{$key}_orderState"));
        }
    }

    public static function setMaxSync($value)
    {
        add_option(pplcz_create_name("watch_phases_max_sync"), intval($value) ?: 200) || update_option(pplcz_create_name("watch_phases_max_sync"), intval($value) ?: 200);
    }


    public static function getPhases()
    {
        $phases = include __DIR__ . '/../config/shipment_phases.php';
        $phases = array_map(function ($item, $key) {
            $output = new \PPLCZ\Model\Model\ShipmentPhaseModel();
            $output->setCode($key);
            $output->setTitle($item);
            $output->setWatch(!!get_option(pplcz_create_name("watch_phases_{$key}")));
            $output->setOrderState(get_option(pplcz_create_name("watch_phases_{$key}_orderState")) ?: null);
            return $output;
        }, $phases, array_keys($phases));

        $maxSync = get_option(pplcz_create_name("watch_phases_max_sync"));
        $maxSync = intval($maxSync) ?: 200;

        $sync = new SyncPhasesModel();

        $sync->setPhases($phases);
        $sync->setMaxSync($maxSync);

        return $sync;
    }
}