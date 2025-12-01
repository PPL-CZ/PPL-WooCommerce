<?php
namespace PPLCZ\ModelCPLNormalizer;

use PPLCZCPL\Model\EpsApiMyApi2WebModelsOrderBatchOrderModelSender;
use PPLCZCPL\Model\EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel;
use PPLCZ\Data\AddressData;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CPLBatchAddressDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof AddressData && $type === EpsApiMyApi2WebModelsOrderBatchOrderModelSender::class) {
            $sender = new EpsApiMyApi2WebModelsOrderBatchOrderModelSender();
            $sender->setName($data->get_name());
            $sender->setCity($data->get_city());
            $sender->setStreet($data->get_street());
            $sender->setZipCode($data->get_zip());
            $sender->setEmail($data->get_mail());
            $sender->setPhone($data->get_phone());
            $sender->setCountry($data->get_country());
            $sender->setContact($data->get_contact());
            return $sender;
        }

        if ($data instanceof AddressData && $type === EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel::class) {
            $recepient = new EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel();

            $name = preg_split("~\s+~", trim($data->get_name() ?: ""));
            $add = [];

            while ($name)
            {
                $add[] = array_shift($name);
                if (mb_strlen(join(' ', $add)) < 50)
                {
                    $recepient->setName(join (' ', $add));
                }
                else
                {
                    $name = array_merge([array_pop($add)], $name);
                    break;
                }
            }

            $add = [];

            while ($name)
            {
                $add[] = array_shift($name);
                if (mb_strlen(join(' ', $add)) < 50)
                {
                    $recepient->setName2(join (' ', $add));
                }
                else
                {
                    $name = array_merge(array_pop($add), $name);
                    break;
                }
            }

            $recepient->setContact(trim($data->get_contact() ?: ""));
            $recepient->setPhone($data->get_phone());
            $recepient->setEmail($data->get_mail());
            $recepient->setCity(trim($data->get_city() ?: ""));
            $recepient->setZipCode($data->get_zip());
            $recepient->setStreet(trim($data->get_street() ?: ""));
            $recepient->setCountry($data->get_country());
            return $recepient;
        }
        throw new \Exception();
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof AddressData && $type === EpsApiMyApi2WebModelsOrderBatchOrderModelSender::class
            || $data instanceof AddressData && $type === EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel::class;
    }
}