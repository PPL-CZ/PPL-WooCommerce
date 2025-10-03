<?php
namespace PPLCZ\Validator;
defined("WPINC") or die();

use PPLCZ\Model\Model\RecipientAddressModel;
use PPLCZ\Model\Model\SenderAddressModel;

class AddressModelValidator extends ModelValidator {

    public function canValidate($model)
    {
        return $model instanceof RecipientAddressModel
            || $model instanceof SenderAddressModel;

    }

    public function validate($model, $error, $path)
    {

        $basePrefix = $model instanceof RecipientAddressModel ? "U příjemce " : "U odesílatele ";

        foreach ([
                    "name" => "{$basePrefix}Osoba/firma musí být vyplněna",
                    "street" => "{$basePrefix}Ulice musí být vyplněna",
                    "city" => "{$basePrefix}Město musí být vyplněno",
                    "country" => "{$basePrefix}Není určen stát"
                 ] as $property => $message) {

            if (!$model->isInitialized($property) || !$model->{"get{$property}"}())
            {
                $error->add($path . ".{$property}", $message);
            }

        }

        if (!self::isZip($model->getCountry(), $model->getZip()))
        {
            $error->add($path . ".zip","{$basePrefix} Je problematické PSČ pro danou zemi");
        }

        if ($model->isInitialized("mail") && $model->getMail())
        {
            if (!filter_var($model->getMail(), FILTER_VALIDATE_EMAIL))
                $error->add("$path.mail", "{$basePrefix} není určen validní mail");
        }

        if ($model->isInitialized("phone") && $model->getPhone())
        {
            $phone = $model->getPhone();
            if (!$this->isPhone($phone)) {
                $error->add("$path.phone", "{$basePrefix} není správné telefonní číslo");
            }
        } else if ($model instanceof RecipientAddressModel) {
           $error->add("$path.phone", "{$basePrefix}  nesmí zůstat prázdné telefonnní číslo");
        }

        if ($model instanceof SenderAddressModel) {
            if (!$model->isInitialized("addressName") || !$model->getAddressName())
            {
                $error->add($path . ".addressName", "U odesílatele je potřeba vyplnit název adresy");
            }
        }
    }
}