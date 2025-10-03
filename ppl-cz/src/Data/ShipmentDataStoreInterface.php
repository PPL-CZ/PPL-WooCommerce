<?php
namespace PPLCZ\Data;
defined("WPINC") or die();


interface ShipmentDataStoreInterface
{

    public function read_shipments($args=[]);

    public function read_order_shipments($order_id);

    public function read_batch_shipments($batch_id);


    public function read_label_groups();
}