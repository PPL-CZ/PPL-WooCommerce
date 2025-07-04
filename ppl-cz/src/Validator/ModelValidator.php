<?php
namespace PPLCZ\Validator;
defined("WPINC") or die();

use PPLCZ\Model\Model\BankAccountModel;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentModel;

abstract class ModelValidator {
    /**
     * @param mixed $model
     * @return bool
     */
    public abstract function canValidate($model);

    /**
     * @param mixed $model
     * @param \WP_Error $errors
     * @param string $path
     * @return void
     */
    public abstract function validate($model, $errors, $path);

    public function getValue($model, $property)
    {
        /**
         * @var UpdateShipmentModel $modelPath
         */
        foreach (explode(".", $property) as $modelPath) {
            if(is_callable([$model,"isInitialized"]))
            {
                if ($model->isInitialized($modelPath)) {
                    $model = $model->{"get{$modelPath}"}();
                    if (!$model)
                        return null;
                }
                else {
                    return null;
                }
            }
            else if (is_array($model))
            {
                if (isset($model[$modelPath]))
                    $model = $model[$modelPath];
                else
                    return null;
            }
            else {
                throw new \Exception("Cesta vs model");
            }
        }
        return $model;
    }


    protected static $validators = [
        AddressModelValidator::class,
        CartValidator::class,
        PackageValidator::class,
        ShipmentValidator::class,
        CollectionValidator::class,
        CodValidator::class,
        InsuranceValidator::class,
        ParcelValidator::class
    ];

    public function isCurrency($value)
    {
        return isset(get_woocommerce_currencies()[$value]);
    }

    public function  isEmail($email)
    {
        $email = trim($email);
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function isContact($name){
        $name = trim($name);
        return !!preg_match('/^([\p{Latin}\p{Cyrillic}\'’\.\-]+(?:\s+[\p{Latin}\p{Cyrillic}\'’\.\-]+)+)$/u', $name);
    }


    public function isPhone($phoneNumber)
    {
        $phoneNumber = preg_replace('/[\s\-()]/', '', $phoneNumber);

        // Použít regulární výraz pro validaci formátu telefonního čísla
        $pattern = '/^\+?[0-9]{9,14}$/';

        if (preg_match($pattern, $phoneNumber)) {
            return true;
        } else {
            return false;
        }
    }

    public function isZip($countryCode, $zip)
    {
        $content ="GB ^(([gG][iI][rR] {0,}0[aA]{2})|((([a-pr-uwyzA-PR-UWYZ][a-hk-yA-HK-Y]?[0-9][0-9]?)|(([a-pr-uwyzA-PR-UWYZ][0-9][a-hjkstuwA-HJKSTUW])|([a-pr-uwyzA-PR-UWYZ][a-hk-yA-HK-Y][0-9][abehmnprv-yABEHMNPRV-Y]))) {1,}[0-9][abd-hjlnp-uw-zABD-HJLNP-UW-Z]{2}))$
CZ ^[1-7]\d{2}[ ]?\d{2}$
DE ^\d{5}$
SK ^[8|9|0]\d{2}[ ]?\d{2}$
AT ^\d{4}$
PL ^\d{2}[\- ]?\d{3}$
CH ^\d{4}$
FI ^\d{5}$
HU ^\d{4}$
SI ^\d{4}$
LV ^\d{4}$
EE ^\d{5}$
LT ^\d{5}$
BE ^\d{4}$
DK ^\d{4}$
ES ^\d{5}$
FR ^\d{2}[ ]?\d{3}$
IT ^\d{5}$
NL ^\d{4}[ ]?[A-Z]{2}$
NO ^\d{4}$
PT ^\d{4}([\-]\d{3})?$
SE ^\d{3}[ ]?\d{2}$
RO ^\d{6}$
BG ^\d{4}$
GR ^\d{3}[ ]?\d{2}$
RU ^\d{6}$
TR ^\d{5}$
LU ^\d{4}$
HR ^\d{5}$
CY ^\d{4}$
IE ^.+$
USA ^.+$
CN ^.+$";

        $zips = array_filter(array_map("trim", preg_split("~\r?\n~", $content)));
        $zips = array_map(function ($item) {
            if (preg_match('~([A-Z]{2}) (.+)~', $item, $args))
            {
                return [$args[1], $args[2]];
            }
            return null;
        }, $zips);

        $zips = array_filter($zips, function ($item) use ($countryCode) {
            $isCountry =  $item[0] === $countryCode;
            return $isCountry;
        });

        if (!$zips)
            return true;

        $hasZip = reset($zips);

        return preg_match("~{$hasZip[1]}~", $zip);
    }
}