<?php
namespace PPLCZ\Data;
defined("WPINC") or die();


interface CollectionDataStoreInterface
{

    public function read_collections($args);

    public function available_collections();

    public function last_collection();

    public static function find_reference_for_date($date);
}