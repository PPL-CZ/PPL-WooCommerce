<?php

namespace PPLCZ\Validator;
defined("WPINC") or die();

use PPLCZ\Data\ParcelData;
use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentModel;
use PPLCZ\Setting\MethodSetting;
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
        $method = MethodSetting::getMethod($code);
        /**
         * @var ShipmentModel|UpdateShipmentModel $model
         */
        if (!$code || !$method)
            return;

        $parcelid = $this->getValue($model, "parcelId") ?: $this->getValue($model, "parcel.id");

        if ($model instanceof ShipmentModel) {

            if (!$method || !$method->getParcelRequired()) {
                if ($this->getValue($model, "hasParcel")) {
                    $errors->add("$path.hasParcel", "Metoda neumožňuje výběr výdejního místa");
                }
            } else if ($this->getValue($model, "hasParcel") && !$this->getValue($model, "parcel")) {
                $errors->add("$path.hasParcel", "Je potřeba vybrat výdejní místo");
            }

            if ($method->getParcelRequired()) {

                if ($model->getAge()
                    && !in_array('CZ', $method->getCountries(), true)) {
                    //$errors->add("$path.age", "Mimo ČR nelze dělat kontrolu věku");
                } else if ($model->getAge() && $model->getHasParcel()) {
                    if ($model->isInitialized("parcel") && $model->getParcel()) {
                        $parcel = $model->getParcel();
                        if ($parcel->getType() !== "ParcelShop") {
                            $errors->add("$path.hasParcel", "Výdejní misto může být pouze obchod kvůli kontrole věku");
                        }
                    }
                }


                $country = $this->getValue($model, "recipient.country");
                if ($country) {
                    if (!in_array($country, $method->getCountries(), true)) {
                        if ($country === 'CZ')
                            $errors->add("$path.hasParcel", "Služba není určena pro dopravu z České republiky do zahraničí");
                        else
                            $errors->add("$path.hasParcel", "Služba není určena pro dopravu v rámci České republiky");
                    }

                }
                if ($parcelid) {
                    $parcelData = new ParcelData($parcelid);
                    if ($parcelData) {
                        $availableParcels = $method->getAvailableParcelTypes();
                        if (!$availableParcels || !in_array($parcelData->get_type(), $availableParcels, true)) {
                            $errors->add("$path.hasParcel", "Metoda nepodporuje dané výdejní místo");
                        }
                        $country = $parcelData->get_country();
                        if (!in_array($country, $method->getCountries(), true)) {
                            $errors->add("$path.hasParcel", "Výdejní místo je mimo dosah dané služby");
                        }
                        $country = $this->getValue($model, "recipient.country");
                        if ($country && $parcelData->get_country() !== $country)
                        {
                            $errors->add("$path.hasParcel", "Neshoduje se země příjemce a parcelshop/boxu");
                        }
                    }

                }
            }

        }

    }
}