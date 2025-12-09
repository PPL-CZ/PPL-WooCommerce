<?php
namespace PPLCZ\Setting;

use PPLCZ\Admin\CPLOperation;
use PPLCZ\Model\Model\ParcelPlacesModel;
use PPLCZ\Model\Model\ShipmentMethodModel;
use PPLCZ\Model\Model\GlobalSettingModel;

class MethodSetting
{

    public static function getGlobalSetting()
    {
        $globalSetting = new GlobalSettingModel();

        $key1 = pplcz_create_name("use_order_number_in_packages");
        $key2 = pplcz_create_name("use_order_number_in_variable_number");

        $value1 = get_option($key1);

        if ($value1 !== 'yes' && $value1 !== 'no')
            $value1 = 'no';
        $globalSetting->setUseOrderNumberInPackages($value1 === 'yes');

        $value2 = get_option($key2);

        if ($value2 !== 'yes' && $value2 !== 'no')
            $value2 = 'no';

        $globalSetting->setUseOrderNumberInVariableSymbol($value2 === 'yes');

        return $globalSetting;

    }

    public static function setGlobalSetting(GlobalSettingModel $globalSettingModel)
    {
        $key1 = pplcz_create_name("use_order_number_in_packages");
        $key2 = pplcz_create_name("use_order_number_in_variable_number");

        $value1 = $globalSettingModel->getUseOrderNumberInPackages() ? 'yes': 'no';
        $value2 = $globalSettingModel->getUseOrderNumberInVariableSymbol() ? 'yes': 'no';

        add_option($key1, $value1) || update_option($key1, $value1);
        add_option($key2, $value2) || update_option($key2, $value2);
    }

    public static function getGlobalParcelboxesSetting()
    {
        $parcelPlaces = new ParcelPlacesModel();

        $disabledParcelBox = !!get_option(pplcz_create_name("disabled_parcelbox"));
        $disabledParcelShop = !!get_option(pplcz_create_name("disabled_parcelshop"));
        $disabledAlzaBox = !!get_option(pplcz_create_name("disabled_alzabox"));
        $disabledByStripe = !!get_option(pplcz_create_name("disabled_by_stripe"));
        $disabledCountriesFromBaseSetting = get_option(pplcz_create_name("disabled_parcel_countries"));
        if (!is_array($disabledCountriesFromBaseSetting))
            $disabledCountriesFromBaseSetting = [];

        $languageMap = pplcz_create_name("map_language");

        $parcelPlaces->setDisabledByStripe($disabledByStripe);
        $parcelPlaces->setDisabledCountries($disabledCountriesFromBaseSetting);
        $parcelPlaces->setMapLanguage($languageMap);
        $parcelPlaces->setDisabledParcelBox($disabledParcelBox);
        $parcelPlaces->setDisabledParcelShop($disabledParcelShop);
        $parcelPlaces->setDisabledAlzaBox($disabledAlzaBox);

        return $parcelPlaces;
    }

    public static function setGlobalParcelboxesSetting(ParcelPlacesModel $setting)
    {
        $parcelbox = pplcz_create_name("disabled_parcelbox");
        $parcelshop =pplcz_create_name("disabled_parcelshop");
        $alzabox = pplcz_create_name("disabled_alzabox");
        $disabledByStripe = pplcz_create_name("disabled_by_stripe");
        $disabledCountries = pplcz_create_name("disabled_parcel_countries");
        $languageMap = pplcz_create_name("map_language");
        add_option($disabledByStripe, $setting->getDisabledByStripe()) || update_option($disabledByStripe, $setting->getDisabledByStripe());
        add_option($parcelbox, $setting->getDisabledParcelBox()) || update_option($parcelbox, $setting->getDisabledParcelBox());
        add_option($parcelshop, $setting->getDisabledParcelShop()) || update_option($parcelshop, $setting->getDisabledParcelShop());
        add_option($alzabox, $setting->getDisabledAlzaBox()) || update_option($alzabox, $setting->getDisabledAlzaBox());
        add_option($disabledCountries, $setting->getDisabledCountries()) || update_option($disabledCountries, $setting->getDisabledCountries());
        add_option($languageMap, $setting->getMapLanguage()) || update_option($languageMap, $setting->getMapLanguage());
    }

    public static function getCodMethods($code) {
        $methods = [
            "PRIV" => "PRID",
            "CONN" => "COND",
            "SMAR" => "SMAD",
            "SMEU" => "SMED",
            "SBOX" => "SBOD"
        ];

        if (isset($methods[$code]))
            return $methods[$code];
        return null;
    }

    public static function getMethod($code)
    {
        foreach (static::getMethods() as $method)
        {
            if ($method->getCode() === $code)
                return $method;
        }
        return null;
    }

    private static $_methods = null;

    /**
     * @return ShipmentMethodModel[]
     */
    public static function getMethods()
    {
        if (self::$_methods !== null) return self::$_methods;

        $output = [];

        $description = [
            "PRID" => "Doprava v rámci České republiky na adresu",
            "SMAD" => "Doprava v rámci České republiky na výdejní místo",
            "SBOD" => "Doprava v rámci České republiky pouze do ParcelBoxu (váha omezena do 10 kg a rozměry max 50×40×38cm)",
            "SMED" => "Doprava v rámci Polska, Německa, Slovenska na výdejní místo",
            "COND" => "Doprava v rámci EU na adresu",
            "COPD" => "Doprava mimo EU v rámci Evropy",
        ];



        $methods = [
            "PRIV"=> "PPL Parcel CZ Private", // cz
            "SMAR" => "PPL Parcel CZ Smart", // cz, VM

            "SMEU" => "PPL Parcel Smart Europe", // necz
            "CONN" => "PPL Parcel Connect", // necz,

            "COPL" => "PPL Parcel Connect Plus",

            "SBOX" => "PPL Parcel CZ Smart To Box"
        ];

        foreach ($methods as $key => $value) {
            $method = new ShipmentMethodModel();

            $method->setCode($key);
            $method->setTitle($value);
            $method->setDescription($description[substr($key, 0, 3) . 'D']);
            $method->setCodAvailable(false);
            $method->setParcelRequired(in_array($key, ["SMAR", "SMEU", "SBOX"], true));
            $method->setAgeValidation(null);

            if (in_array($key, ["SMAR", "PRIV"], true)) {
                $method->setAgeValidation(true);
            } else if ($key === "SBOX") {
                $method->setAgeValidation(false);
                $method->setMaxWeight(10);
                $method->setMaxDimension([50, 40, 38 ]);
            } else if (in_array($key, ["SMEU", "CONN"], true)) {
                $method->setMaxPackages(1);
            }

            if($method->getParcelRequired())
            {
                $method->setDisabledParcelTypes([]);
                $method->setAvailableParcelTypes(["ParcelBox", "ParcelShop", "AlzaBox"]);

                if ($method->getCode() === "SBOX") {
                    $method->setDisabledParcelTypes([ "AlzaBox", "ParcelShop"]);
                    $method->setAvailableParcelTypes(["ParcelBox"]);
                    $method->setMaxPackages(1);
                }
            }
            $output[] = $method;
        }

        $codMethods = [
            "PRID"=> "PPL Parcel CZ Private - dobírka", // cz
            "SMAD" => "PPL Parcel CZ Smart - dobírka", // cz, VM
            "SMED" => "PPL Parcel Smart Europe - dobírka", // necz
            "COND" => "PPL Parcel Connect - dobírka", // necz,
            "SBOD" => "PPL Parcel CZ Smart To Box - dobírka"
        ];

        foreach ($codMethods as $key => $value) {
            $method = new ShipmentMethodModel();
            $method->setCode($key);
            $method->setTitle($value);
            $method->setCodAvailable(true);
            $method->setDescription($description[$key]);
            $method->setParcelRequired(in_array($key, ["SMAD", "SMED", "SBOD"], true));
            $method->setAgeValidation(null);

            if (in_array($key, ["SMAD", "PRID"], true)) {
                $method->setAgeValidation(true);
            } else if ($key === "SBOD") {
                $method->setAgeValidation(false);
                $method->setMaxWeight(10);
                $method->setMaxDimension([50, 40, 38 ]);
                $method->setMaxPackages(1);
            }else if (in_array($key, ["SMED", "COND"], true)) {
                $method->setMaxPackages(1);
            }

            if($method->getParcelRequired())
            {
                $method->setAvailableParcelTypes(["ParcelBox", "ParcelShop", "AlzaBox"]);
                $method->setDisabledParcelTypes([]);
                if ($method->getCode() === "SBOD") {
                    $method->setAvailableParcelTypes(["ParcelBox"]);
                    $method->setDisabledParcelTypes([ "AlzaBox", "ParcelShop"]);
                }
            }

            $output[] = $method;
        }

        foreach ($output as $value)
        {
            $code = $value->getCode();
            $countries = [];
            if (in_array($code, ['SMAR',"SMAD", 'PRIV', 'PRID', "SBOX", "SBOD" ]))
                $countries = ["CZ"];
            else if (in_array($code,['SMEU',"SMED", 'CONN', 'COND']))
                $countries = self::getEuCountries();
            else {
                $countries = require __DIR__ . '/../config/countries.php';
                $countries = array_diff(array_keys($countries), self::getEuCountries());
            }
            $value->setCountries($countries);
        }

        return self::$_methods = $output;
    }

    public static function getEuCountries()
    {
        return [
            'AT', // Rakousko
            'BE', // Belgie
            'BG', // Bulharsko
            'HR', // Chorvatsko
            'CY', // Kypr
            'DK', // Dánsko
            'EE', // Estonsko
            'FI', // Finsko
            'FR', // Francie
            'DE', // Německo
            'GR', // Řecko
            'HU', // Maďarsko
            'IE', // Irsko
            'IT', // Itálie
            'LV', // Lotyšsko
            'LT', // Litva
            'LU', // Lucembursko
            'MT', // Malta
            'NL', // Nizozemsko
            'PL', // Polsko
            'PT', // Portugalsko
            'RO', // Rumunsko
            'SK', // Slovensko
            'SI', // Slovinsko
            'ES', // Španělsko
            'SE', // Švédsko
        ];
    }

    public static function getMethodForCountry($country, $method)
    {

        $countries = require __DIR__ . '/../config/countries.php';
        if (!isset($countries[$country]))
            return null;

        if ($country === 'CZ')
        {
            $codes = [
                'PRIV' => 'PRIV', 'PRID'=> "PRID", 'SMAR'=> 'SMAR', 'SMAD'=> 'SMAD',
                'SMEU' => "SMAR", "SMED" => "SMAD", "CONN" => "PRIV", 'COND'=> "PRID",
                "COPL" => "PRIV", "SBOX" => "SBOX", "SBOD" => "SBOD"
            ];
            if (isset($codes[$method]))
                return $codes[$method];
            return null;

        }
        else
        {
            if (in_array($country, self::getEuCountries(), true))
            {
                $codes = [
                    "COPL" => "CONN",
                    'PRIV' => 'CONN', 'PRID'=> "COND", 'SMAR'=> 'SMEU', 'SMAD'=> 'SMED',
                    'SMEU' => "SMEU", "SMED" => "SMED", "CONN" => "CONN", 'COND'=> "COND",
                ];

                if (isset($codes[$method]))
                    return $codes[$method];

                return null;
            }
            else
            {
                if (in_array($method, ['COPL', "PRIV", "CONN"], true))
                    return "COPL";
                return null;
            }
        }
    }

}