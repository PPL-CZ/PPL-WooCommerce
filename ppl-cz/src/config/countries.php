<?php
defined("WPINC") or die();


return call_user_func(function() {
    $countries = get_transient(pplcz_create_name("countries_limit"));
    if (!$countries || defined("PPLCZ_REFRESH")) {
        try {
            $cpl = new \PPLCZ\Admin\CPLOperation();
            $countries = $cpl->getCountries();
            if ($countries) {
                set_transient(pplcz_create_name("countries_limit"), $countries);
                return $countries;
            }
        }
        catch (Exception $ex)
        {

        }
    }

    return [
        'CZ' => true,
        'DE' => false,
        'GB' => false,
        'SK' => true,
        'AT' => false,
        'PL' => true,
        'CH' => false,
        'FI' => false,
        'HU' => true,
        'SI' => false,
        'LV' => false,
        'EE' => false,
        'LT' => false,
        'BE' => false,
        'DK' => false,
        'ES' => false,
        'FR' => false,
        'IE' => false,
        'IT' => false,
        'NL' => false,
        'NO' => false,
        'PT' => false,
        'SE' => false,
        'RO' => true,
        'BG' => false,
        'GR' => false,
        'LU' => false,
        'HR' => false,
    ];
});