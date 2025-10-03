<?php
namespace PPLCZ\Data;

defined("WPINC") or die();


class AddressDataStore extends PPLDataStore
{
    protected $id_name = "ppl_address_id";

    protected $table_name = "address";


    public static function stores($stores)
    {
        return array_merge($stores, ["pplcz-address" =>  self::class]);
    }

    public static function register()
    {
        add_filter('woocommerce_data_stores', [self::class, "stores"]);
    }

}