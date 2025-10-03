<?php
namespace  PPLCZ\Validator;

defined("WPINC") or die();

use PPLCZ\Model\Model\ShipmentModel;
use PPLCZ\Model\Model\UpdateShipmentModel;

class InsuranceValidator extends ModelValidator
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

        $limits = include __DIR__ . '/../config/limits.php';
        $insuranceLimits = array_filter($limits["INSURANCE"], function ($item) use ($code) {
            return  $code === $item['product'];
        });
        $insuranceLimits = reset($insuranceLimits);
        if ($model->isInitialized("packages")) {
            $packages = $model->getPackages();

            foreach ($packages as $key => $package) {
                if ($package->getInsurance()) {
                    $insurance = $package->getInsurance();
                    if (!$insuranceLimits && $insurance) {
                        $errors->add("$path.packages.{$key}.insurance", "Nelze nastavovat pojištění.");
                        continue;
                    }
                    if ($insurance && $insurance < $insuranceLimits["min"]) {
                        $errors->add("$path.packages.{$key}.insurance", "Minimální částka při pojištění je {$insuranceLimits['min']}CZK.");
                        continue;
                    }
                    if ($insurance && $insurance > $insuranceLimits["max"]) {
                        $errors->add("$path.packages.{$key}.insurance", "Maximální částka při pojištění je {$insuranceLimits['max']}CZK.");
                        continue;
                    }
                }
            }
        }
    }
}