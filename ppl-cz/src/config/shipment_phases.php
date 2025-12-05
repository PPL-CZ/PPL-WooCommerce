<?php
defined("WPINC") or die();


return call_user_func(function() {

    return [
        "Order" => "Objednávka",
        "InTransport" => "V přepravě",
        "Delivering" => "Na cestě",
        "PickupPoint" => "Na výdejním místě",
        "Delivered" => "Doručeno",
        "Returning"=> "Na cestě zpět odesílateli",
        "BackToSender" => "Vráceno odesílateli",
        "Canceled" => "Zrušeno",
        "Deleted" => "Zrušeno"
    ];
});