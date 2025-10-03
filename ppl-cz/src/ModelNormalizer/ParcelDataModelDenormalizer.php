<?php
namespace PPLCZ\ModelNormalizer;

defined("WPINC") or die();

use PPLCZCPL\Model\EpsApiMyApi2WebModelsAccessPointAccessPointModel;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZ\Data\ParcelData;
use PPLCZ\Model\Model\ParcelAddressModel;

class ParcelDataModelDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if (
            $data instanceof EpsApiMyApi2WebModelsAccessPointAccessPointModel
            && $type === ParcelData::class)
        {
            $model = $context['data'] ?: new ParcelData();
            $model->set_type($data->getAccessPointType());
            $model->set_lat($data->getGps()->getLatitude());
            $model->set_lng($data->getGps()->getLongitude());
            $model->set_name($data->getName() );
            $model->set_name2($data->getName2());
            $model->set_street($data->getStreet());
            $model->set_city($data->getCity());
            $model->set_zip($data->getZipCode());
            $model->set_code($data->getAccessPointCode());
            $model->set_country($data->getCountry());
            $model->set_valid(true);



            return $model;
        }
        else if  ($data instanceof  ParcelData && $type === ParcelAddressModel::class)
        {
            $model = new ParcelAddressModel();
            $model->setCity($data->get_city());
            $model->setName($data->get_name());
            if ($data->get_name2())
                $model->setName2($data->get_name2());
            $model->setZip($data->get_zip());
            $model->setStreet($data->get_street());
            $model->setCountry($data->get_country());
            $model->setLat($data->get_lat());
            $model->setLng($data->get_lng());
            $model->setType($data->get_type());
            $model->setId($data->get_id());
            return $model;
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        $ex = 0;
        if (
            $data instanceof EpsApiMyApi2WebModelsAccessPointAccessPointModel
            && $type === ParcelData::class)
        {
            return true;
        } else if ($data instanceof  ParcelData && $type === ParcelAddressModel::class) {
            return true;
        }
        // TODO: Implement supportsDenormalization() method.
    }
}