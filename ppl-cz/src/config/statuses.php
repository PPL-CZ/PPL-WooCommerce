<?php
defined("WPINC") or die();


return call_user_func(function() {

    $statuses = get_transient(pplcz_create_name("shipment_statuses")) ?: [];

    if (!$statuses || defined("PPLCZ_REFRESH") )
    {
        try {
            $cpl = new \PPLCZ\Admin\CPLOperation();
            $statuses = $cpl->getStatuses();
            if ($statuses) {
                set_transient(pplcz_create_name("shipment_statuses"), $statuses);
                return $statuses;
            }
        }
        catch (Exception $ex)
        {
        }
    }

    return $statuses;
});