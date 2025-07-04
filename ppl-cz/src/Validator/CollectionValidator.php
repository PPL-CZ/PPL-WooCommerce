<?php
namespace PPLCZ\Validator;
defined("WPINC") or die();
use PPLCZ\Model\Model\NewCollectionModel;

class CollectionValidator extends ModelValidator
{

    public function canValidate($model)
    {
        return $model instanceof NewCollectionModel;
    }

    public function validate($model, $errors, $path)
    {

        /**
         * @var NewCollectionModel $model
         */
        if (!$model->isInitialized("sendDate") || !$model->getSendDate())
            $errors->add("$path.sendDate", "Datum svozu nesmí být prázdné");
        else {
            $datum = new \DateTime($model->getSendDate());
            $datum = new \DateTime($datum->format("Y-m-d"));

            $today = new \DateTime();
            $today = new \DateTime($today->format('Y-m-d'));
            $today9hour = (new \DateTime($today->format('Y-m-d')));
            $today9hour->setTime(9, 0, 0);
            if ($today > $datum
                || $today == $datum && new \DateTime() >= $today9hour)
            {
                $errors->add("$path.sendDate", "Svoz je příliš brzy");
            }
        }

        if ($model->isInitialized("estimatedShipmentCount") && $model->getEstimatedShipmentCount() > 100)
        {
            $errors->add("$path.estimatedShipmentCount", "Příliš mnoho zásilek pro svoz");
        } else if (!$model->isInitialized("estimatedShipmentCount") || $model->getEstimatedShipmentCount() <= 0)
        {
            $errors->add("$path.estimatedShipmentCount", "Příliš málo zásilek pro svoz");
        }

        $email =  $this->getValue($model, 'email');

        if (!$this->isEmail($email))
        {
            $errors->add("$path.email", "Zadejte prosím platnou emailovou adresu.");
        }

        $telephone = $this->getValue($model, 'telephone');

        if (!$this->isPhone($telephone)) {
            $errors->add("$path.telephone", "Zadejte prosím platné telefonní číslo.");
        }

        $contact = $this->getValue($model, 'contact');

        if (!$this->isContact($contact)) {
                $errors->add("$path.contact", "Zadejte prosím platné kontaktní údaje.");
        }



    }
}