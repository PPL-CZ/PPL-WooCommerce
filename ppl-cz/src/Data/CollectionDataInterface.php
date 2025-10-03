<?php
namespace PPLCZ\Data;

defined("WPINC") or die();


interface CollectionDataInterface
{

    public static function read_collections($args);

    public static function available_collections();

    public static function last_collection();

}