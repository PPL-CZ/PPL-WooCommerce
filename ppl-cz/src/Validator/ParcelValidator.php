<?php
namespace PPLCZ\Validator;
defined("WPINC") or die();
use PPLCZ\Data\ParcelData;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentModel;
use PPLCZ\ShipmentMethod;

class ParcelValidator extends ModelValidator
{

    public function canValidate($model)
    {
        return $model instanceof ShipmentModel || $model instanceof UpdateShipmentModel;
    }

    public function validate($model, $errors, $path)
    {
        $code = $this->getValue($model, 'serviceCode');
        /**
         * @var ShipmentModel|UpdateShipmentModel $model
         */

        if ($model->isInitialized("age")
            && $model->getAge()
            && in_array($code, ["SMEU", "SMED", "CONN", "COND"])) {
            //$errors->add("$path.age", "Mimo ČR nelze dělat kontrolu věku");
        }
        else if ($model->isInitialized("age") && $model->getAge()
            && $model->isInitialized("hasParcel") && $model->getHasParcel()) {
            if ($model->isInitialized("parcel") && $model->getParcel()) {
                $parcel = $model->getParcel();
                if ($parcel->getType() !== "ParcelShop") {
                    $errors->add("$path.hasParcel", "Výdejní misto může být pouze obchod kvůli kontrole věku");
                }
            }
        }

        $parcelid = $this->getValue($model, "parcelId") ?: $this->getValue($model, "parcel.id");
        $code = $this->getValue($model, 'serviceCode');

        if (in_array($code, ["SMAD", "SMAR"])) {
            if ($parcelid) {
                $parcelData = new ParcelData($parcelid);
                if ($parcelData->get_country() !== "CZ") {
                    $errors->add("$path.hasParcel", "Pro službu lze vybrat pouze české výdejní místo");
                }
            }
        } else if (in_array($code, ["SMEU", "SMED"])) {
            if ($parcelid) {
                $parcelData = new ParcelData($parcelid);
                if ($parcelData->get_country() === "CZ") {
                    $errors->add("$path.hasParcel", "Pro službu lze vybrat pouze zahraniční výdejní místo");
                }
            }
        }

        if ($model instanceof ShipmentModel) {
            if ($model->isInitialized("serviceCode") && $model->getServiceCode()) {
                $code = $model->getServiceCode();
                if (!ShipmentMethod::isMethodWithParcel($code)) {
                    if ($this->getValue($model, "hasParcel")) {
                        $errors->add("$path.hasParcel", "Metoda neumožňuje výběr výdejního místa");
                    }
                } else if ($this->getValue($model, "hasParcel") && !$this->getValue($model, "parcel")) {
                    $errors->add("$path.hasParcel", "Je potřeba vybrat výdejní místo");
                }

                if (in_array($code, ["PRIV", "PRID", "SMAR", "SMAD"]) && $model->isInitialized("recipient"))
                {
                    if ($this->getValue($model, "recipient.country") !== "CZ") {
                        $errors->add("$path.recipient.country", "Služba není určena pro dopravu z České republiky do zahraničí");
                    }
                }
                else if(in_array($code, ["SMEU", "SMED", "CONN", "COND"]) && $model->isInitialized("recipient"))
                {
                    if ($this->getValue($model, "recipient.country") === "CZ") {
                        $errors->add("$path.recipient.country", "Služba není určena pro dopravu v rámci České republiky");
                    }
                }
            }
        }

    }
}