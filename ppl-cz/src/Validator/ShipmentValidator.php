<?php
namespace PPLCZ\Validator;
defined("WPINC") or die();
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentModel;
use PPLCZ\Setting\MethodSetting;
use PPLCZ\ShipmentMethod;

class ShipmentValidator extends ModelValidator
{

    public function canValidate($model)
    {
        return $model instanceof ShipmentModel
                || $model instanceof UpdateShipmentModel;
    }

    public function validate($model, $errors, $path)
    {
        if ($model instanceof UpdateShipmentModel) {
            foreach (["referenceId" => "Reference pro objednávku zásilky nesmí zůstat prázdná", "serviceCode" => "Není vybraná služba"] as $item => $message ) {
                if (!$this->getValue($model, $item)) {
                    $errors->add("$path.{$item}", $message);
                }
            }

            if ($model->getServiceCode()) {
                $code = $model->getServiceCode();
                $method = MethodSetting::getMethod($code);


                $isCod = $method && $method->getCodAvailable();
                if ($isCod) {
                    foreach (["codVariableNumber" => "Variabilní číslo musí být vyplněno", "codValue" => "Hodnota dobírky není určena", "codValueCurrency" => "Není určena měna dobírky", "senderId" =>"Je potřeba určit odesílatele pro etiketu"] as $item => $message) {
                        if (!$this->getValue($model, $item)) {
                            $errors->add("$path.{$item}", $message);
                        }
                    }
                }
                if ($code) {
                    if (in_array($code, ["SMEU", "CONN", "SMED", "COND"])
                        && count($model->getPackages()) > 1) {
                        $errors->add("$path.packages", "Počet zásilek je omezen na jednu");
                    }
                }

            }
            if (!$model->getPackages())
            {
                $errors->add("$path.packages", "Přidejte aspoň jednu zásilku");
            }

            foreach ($model->getPackages() as $key=>$package) {
                \PPLCZ\Validator\Validator::getInstance()->validate($package, $errors, "{$path}.packages.{$key}");
            }


        }


        if ($model instanceof ShipmentModel) {
            /**
             * @var ShipmentModel $model
             */
            foreach (["referenceId" => "Je nutné vyplnit referenci zásilky",
                         "serviceCode" => "Je nutné vybrat službu",
                         "sender" => "Je nutné určit odesílatele pro etiketu",
                         "recipient" => "Není určen příjemce zásilky"] as $item => $message)
            {
                if (!$this->getValue($model, $item)) {
                    $errors->add("$path.{$item}", $message);
                }
                else if ($item === "sender" || $item === "recipient") {
                    \PPLCZ\Validator\Validator::getInstance()->validate($this->getValue($model, $item), $errors, "$path.$item");
                }
            }

            $code = $this->getValue($model, 'serviceCode');

            if ($code) {

                $method = MethodSetting::getMethod($code);
                if ($method)
                {
                    if ($method->getMaxPackages() && $model->getPackages() && $method->getMaxPackages() < count($model->getPackages()))
                        $errors->add("$path.packages", "Počet balíčku může být pouze {$method->getMaxPackages()}");

                    $countries = $method->getCountries();
                    $recipient = $model->getRecipient();
                    if ($recipient)
                        if (!in_array($recipient->getCountry(), $countries, true))
                        {
                            $errors->add("$path.packages", "Nepovolená země {$recipient->getCountry()} pro tuto dopravu ");
                        }
                }



            }



        }
    }
}