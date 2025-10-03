<?php

namespace PPLCZ\Validator;
defined("WPINC") or die();
use PPLCZ\Model\Model\NewCollectionModel;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentModel;
use PPLCZ\ShipmentMethod;

class CodValidator extends ModelValidator
{

    public function canValidate($model)
    {
        return $model instanceof ShipmentModel || $model instanceof UpdateShipmentModel;
    }

    public function validate($model, $errors, $path)
    {
        /**
         * @var ShipmentModel|UpdateShipmentModel $model
         */
        if (!$model->isInitialized("serviceCode"))
            return;

        $code = $model->getServiceCode();
        $isCod = ShipmentMethod::isMethodWithCod($code);
        if (!$isCod)
            return;


        $codCurrency = null;
        $codValue = null;

        foreach (["codVariableNumber" => "Není určený variabilní symbol pro dobírku", "codValue" => "Hodnota dobírky není nastavena", "codValueCurrency" => "Není definovaná měna dobírky"] as $item => $message ) {
            if (!$this->getValue($model, $item)) {
                $errors->add("$path.{$item}", $message);
            } else {
                if ($item === 'codValueCurrency')
                    $codCurrency = $this->getValue($model, $item);
                else if ($item === 'codValue')
                    $codValue = $this->getValue($model, $item);
            }
        }

        if (strlen($model->getCodVariableNumber()) > 10)
        {
            $errors->add("$path.codVariableNumber", "Velikost variabilního symbolu může být max 10 čísel");
        }

        if (!preg_match('/^[0-9]+$/', $model->getCodVariableNumber()))
        {
            $errors->add("$path.codVariableNumber", "Hodnota variabilního symbolu může být jen číslo");
        }

        if ($codCurrency && $codValue) {
            $limits = include __DIR__ . '/../config/limits.php';
            $limits = array_filter($limits["COD"], function ($item) use ($code, $codCurrency) {
                return $item['product'] === $code && $item['currency'] === $codCurrency;
            });
            if (!$limits) {
                $errors->add("$path.codValue", "Nelze použít dobírku pro kombinaci {$codCurrency} a {$codValue}");
            }
            else {
                $limits = reset($limits);
                if ($limits['max'] < $codValue) {
                    $errors->add("$path.codValue", "Částka pro dobírku je příliš vysoká");
                }
            }
        }

    }
}