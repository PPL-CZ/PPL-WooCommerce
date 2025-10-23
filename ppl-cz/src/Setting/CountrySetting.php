<?php
namespace PPLCZ\Setting;

use PPLCZ\Model\Model\CountryModel;

class CountrySetting {

    /**
     * @return CountryModel[]
     */
    public static function getCountries()
    {
        $allowedCountries = pplcz_get_allowed_countries();
        $currencies = pplcz_get_cod_currencies();
        $parcelAllowed = array_keys(pplcz_get_parcel_countries());

        return array_map(function ($value, $key) use ($currencies, $parcelAllowed) {
            $countryModel = new CountryModel();
            $countryModel->setCode($key);
            $countryModel->setTitle($value);
            $countryModel->setParcelAllowed(in_array($key, $parcelAllowed));
            $countryModel->setCodAllowed( array_unique(array_map(function ($item) {
                return $item['currency'];
            }, array_filter($currencies, function ($item) use ($key) {
                return $item['country'] === $key;
            }))));
            return $countryModel;

        }, $allowedCountries, array_keys($allowedCountries));

    }
}