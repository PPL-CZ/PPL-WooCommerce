<?php

namespace PPLCZ\Data;

defined("WPINC") or die();


class CodBankAccountDataStore extends PPLDataStore
{
    protected $id_name = "ppl_cod_bank_account_id";

    protected $table_name = "cod_bank_account";


    public static function stores($stores)
    {
        return array_merge($stores, ["pplcz-cod-bank-account" =>  self::class]);
    }

    public static function register()
    {
        add_filter('woocommerce_data_stores', [self::class, "stores"]);
    }

}