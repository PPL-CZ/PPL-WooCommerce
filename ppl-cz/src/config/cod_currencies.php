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
            }
        }
        catch (\Exception $ex)
        {
            return [];
        }
    }
    if (is_array($currencies)) {
        if (array_filter($currencies, function ($item) {
            return $item['country'] === 'SK' && $item['currency'] === 'EUR';
        }))
        {
            $currencies[] = [
                'country' => 'SK',
                'currency' => 'CZK'
            ];
        }
    }
    else {
        $currencies = [];
    }
    return  $currencies;
});