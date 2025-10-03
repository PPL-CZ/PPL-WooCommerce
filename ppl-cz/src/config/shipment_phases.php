<?php
defined("WPINC") or die();


return call_user_func(function() {

    $phases = get_transient(pplcz_create_name("shipment_phases"));

    if (!$phases || defined("PPLCZ_REFRESH") )
    {
        try {
            $cpl = new \PPLCZ\Admin\CPLOperation();
            $phases = $cpl->getShipmentPhases();
            if ($phases) {
                set_transient(pplcz_create_name("shipment_phases"), $phases);
                return $phases;
            }
        }
        catch (\Exception $ex)
        {

        }
    }

    return [
        "Order" => "Objednávka",
        "InTransport" => "V přepravě",
        "Delivering" => "Na cestě",
        "PickupPoint" => "Na výdejním místě",
        "Delivered" => "Doručeno",
        "Returning"=> "Na cestě zpět odesílateli",
        "BackToSender" => "Vráceno odesílateli",
    ];
});