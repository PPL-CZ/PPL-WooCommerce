<?php
namespace PPLCZ\Setting;

use PPLCZ\Model\Model\ParcelPlacesModel;
use PPLCZ\Model\Model\ShipmentMethodModel;

class MethodSetting
{

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
            "SMEU" => "SMED"
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

    /**
     * @return ShipmentMethodModel[]
     */
    public static function getMethods()
    {
        $output = [];

        $methods = [
            "PRIV"=> "PPL Parcel CZ Private", // cz
            "SMAR" => "PPL Parcel CZ Smart", // cz, VM

            "SMEU" => "PPL Parcel Smart Europe", // necz
            "CONN" => "PPL Parcel Connect", // necz,

            "COPL" => "PPL Parcel Connect Plus"
        ];

        foreach ($methods as $key => $value) {
            $method = new ShipmentMethodModel();
            $method->setCode($key);
            $method->setTitle($value);
            $method->setCodAvailable(false);
            $method->setParcelRequired(in_array($key, ["SMAR", "SMEU"], true));
            $output[] = $method;
        }

        $codMethods = [
            "PRID"=> "PPL Parcel CZ Private - dobírka", // cz
            "SMAD" => "PPL Parcel CZ Smart - dobírka", // cz, VM
            "SMED" => "PPL Parcel Smart Europe - dobírka", // necz
            "COND" => "PPL Parcel Connect - dobírka", // necz
        ];

        foreach ($codMethods as $key => $value) {
            $method = new ShipmentMethodModel();
            $method->setCode($key);
            $method->setTitle($value);
            $method->setCodAvailable(true);
            $method->setParcelRequired(in_array($key, ["SMAD", "SMED"], true));
            $output[] = $method;
        }

        return $output;
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
                "COPL" => "PRIV"
            ];
            if (isset($codes[$method]))
                return $codes[$method];
            return null;

        }
        else
        {
            $eu_countries = [
                'AT', // Rakousko
                'BE', // Belgie
                'BG', // Bulharsko
                'HR', // Chorvatsko
                'CY', // Kypr
                'CZ', // Česká republika
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

            if (in_array($country, $eu_countries, true))
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
                if (in_array($method, ['COPL', "PRIV", "SMAR", "CONN", "SMEU"], true))
                    return "COPL";
                return null;
            }
        }
    }

}