<?php
defined("WPINC") or die();


return call_user_func(function() {

    $currencies = get_transient(pplcz_create_name("cod_currencies"));

    if (!$currencies || defined("PPLCZ_REFRESH")) {
        try {
            $cpl = new \PPLCZ\Admin\CPLOperation();
            $currencies = $cpl->getCodCurrencies();
            if ($currencies) {
                set_transient(pplcz_create_name("cod_currencies"), $currencies);
                return $currencies;
            }
        }
        catch (\Exception $ex)
        {
            return [];
        }
    } else if (is_array($currencies)) {
        return $currencies;
    }
    return  [];
});