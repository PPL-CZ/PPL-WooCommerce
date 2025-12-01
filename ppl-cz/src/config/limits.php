<?php
defined("WPINC") or die();


return call_user_func(function () {
    $limits = get_transient(pplcz_create_name("currency_limits"));
    if (!$limits || defined("PPLCZ_REFRESH")) {
        try {
            $cpl = new \PPLCZ\Admin\CPLOperation();
            $limits = $cpl->getLimits();
            if ($limits) {
                set_transient(pplcz_create_name("currency_limits"), $limits);
                return $limits;
            }
        } catch (Exception $ex) {

        }
    }
    if ($limits)
        return $limits;

    return [
        "INSURANCE" => [
            ["product" => "BUSS", "min" => 50000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "BUSD", "min" => 50000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "DOPO", "min" => 50000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "DOPD", "min" => 50000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "COPL", "min" => 100000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "PRIV", "min" => 50000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "PRID", "min" => 50000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "CONN", "min" => 100000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "COND", "min" => 100000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "RETD", "min" => 50000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "SMAR", "min" => 20000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "SMAD", "min" => 20000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "SMEU", "min" => 100000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "SMED", "min" => 100000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "RECI", "min" => 100000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "RECE", "min" => 100000.01, "max" => 500000, 'currency' => "CZK"],
            ["product" => "SBOD", "min" => 20000.01, "max" => 500000, "currency" => "CZK"],
            ["product" => "SBOX", "min" => 20000.01, "max" => 500000, "currency" => "CZK"],
        ],
        "COD" => [
            ["product" => "BUSD", "min" => 0.01, "max" => 100000, 'currency' => "CZK"],
            ["product" => "DOPD", "min" => 0.01, "max" => 100000, 'currency' => "CZK"],
            ["product" => "PRID", "min" => 0.01, "max" => 100000, 'currency' => "CZK"],
            ["product" => "SMAD", "min" => 0.01, "max" => 100000, 'currency' => "CZK"],
            ["product" => "COND", "min" => 0.01, "max" => 80000, 'currency' => "CZK"],
            ["product" => "SMED", "min" => 0.01, "max" => 80000, 'currency' => "CZK"],
            ["product" => "COND", "min" => 0.01, "max" => 3000, 'currency' => "EUR"],
            ["product" => "SMED", "min" => 0.01, "max" => 3000, 'currency' => "EUR"],
            ["product" => "COND", "min" => 0.01, "max" => 6500, 'currency' => "PLN"],
            ["product" => "SMED", "min" => 0.01, "max" => 6500, 'currency' => "PLN"],
            ["product" => "COND", "min" => 1, "max" => 600000, 'currency' => "HUF"],
            ["product" => "COND", "min" => 0.01, "max" => 10500, 'currency' => "HRK"],
            ["product" => "COND", "min" => 0.01, "max" => 7300, 'currency' => "RON"],
            ["product" => "COND", "min" => 0.01, "max" => 500, 'currency' => "EUR"],
            ["product" => "COND", "min" => 0.01, "max" => 2900, 'currency' => "BGN"],
            ["product" => "SBOD", "min" => 0.01, "max" => 100000, "currency" => "CZK"]

        ]
    ];
});

