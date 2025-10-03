<?php
namespace PPLCZ\ModelNormalizer;

defined("WPINC") or die();

use PPLCZCPL\Model\EpsApiMyApi2WebModelsCustomerAddressModel;
use PPLCZ\Data\AddressData;
use PPLCZ\Data\ParcelData;
use PPLCZ\Model\Model\CollectionAddressModel;
use PPLCZ\Model\Model\ParcelAddressModel;
use PPLCZ\Model\Model\RecipientAddressModel;
use PPLCZ\Model\Model\SenderAddressModel;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AddressModelDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof AddressData) {
            if ($type === RecipientAddressModel::class)
                return $this->AddressDataToRecipientAddressModel($data, $context);
            else if ($type === SenderAddressModel::class)
                return $this->AddressDataToSenderAddressModel($data, $context);
        }
        else if ($data instanceof RecipientAddressModel && $type === AddressData::class)
            return $this->RecipientAddressModelToAddressModel($data, $context);
        else if ($data instanceof SenderAddressModel && $type === AddressData::class)
            return $this->SenderAddressModelToAddressModel($data, $context);
        else if ($type === RecipientAddressModel::class && $data instanceof \WC_Order)
        {
            return $this->OrderToRecipientAddressMode($data, $context);
        }
        else if ($type === CollectionAddressModel::class && $data instanceof EpsApiMyApi2WebModelsCustomerAddressModel )
        {
            return $this->CplCollectionAddressToCollectionAddressModel($data, $type);
        }
    }

    private function OrderToRecipientAddressMode(\WC_Order $order, array $context = [])
    {
        $address = new RecipientAddressModel();

        if ($order->get_billing_email())
            $address->setMail($order->get_billing_email());

        if ($order->get_shipping_phone())
            $address->setPhone($order->get_shipping_phone());
        else if ($order->get_billing_phone())
            $address->setPhone($order->get_billing_phone());

        if ($order->get_shipping_address_1())
            $address->setStreet(trim($order->get_shipping_address_1() . " " . $order->get_shipping_address_2()));
        if ($order->get_shipping_city())
            $address->setCity($order->get_shipping_city());
        if ($order->get_shipping_postcode())
            $address->setZip($order->get_shipping_postcode());
        if ($order->get_shipping_country())
            $address->setCountry($order->get_shipping_country());
        if ($order->get_shipping_company()) {
            $address->setName($order->get_shipping_company());
            if ($order->get_shipping_first_name() || $order->get_shipping_last_name())
                $address->setContact(trim($order->get_shipping_first_name() . ' '  .$order->get_shipping_last_name()));
        } else if ($order->get_shipping_first_name() || $order->get_shipping_last_name()) {
            $address->setName($order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name());
        }


        return $address;
    }

    private function AddressDataToRecipientAddressModel(AddressData $data, array $context = [])
    {
        $address = new RecipientAddressModel();
        $address->setCity($data->get_city());
        $address->setName($data->get_name());
        $address->setZip($data->get_zip());
        $address->setStreet($data->get_street());
        $address->setCountry($data->get_country());

        if ($data->get_phone())
            $address->setPhone($data->get_phone());
        if ($data->get_mail())
            $address->setMail($data->get_mail());
        if ($data->get_contact())
            $address->setContact($data->get_contact());
        return $address;
    }

    private function AddressDataToSenderAddressModel(AddressData $data, array $context = [])
    {
        $address = new SenderAddressModel();

        $address->setCity($data->get_city());
        $address->setName($data->get_name());
        $address->setZip($data->get_zip());
        $address->setStreet($data->get_street());
        $address->setCountry($data->get_country());
        $address->setAddressName($data->get_address_name());

        if ($data->get_phone())
            $address->setPhone($data->get_phone());
        if ($data->get_mail())
            $address->setMail($data->get_mail());
        if ($data->get_contact())
            $address->setContact($data->get_contact());
        if ($data->get_id())
            $address->setId($data->get_id());
        return $address;
    }

    private function RecipientAddressModelToAddressModel(RecipientAddressModel $data, array $context)
    {
        $address = $context["data"] ?? new AddressData();

        if ($address->get_lock()) {
            $address = new AddressData();
            $address->set_type("recipient");
            $address->set_hidden(true);
        } else {
            $address->set_type("recipient");
            $address->set_hidden(true);
        }

        $address->set_name($data->getName());
        if ($data->isInitialized("contact"))
            $address->set_contact($data->getContact());
        if ($data->isInitialized("mail"))
            $address->set_mail($data->getMail());
        if ($data->isInitialized("phone"))
            $address->set_phone($data->getPhone());

        $address->set_street($data->getStreet());
        $address->set_city($data->getCity());
        $address->set_zip($data->getZip());
        $address->set_country($data->getCountry());

        return $address;

    }

    private function SenderAddressModelToAddressModel(SenderAddressModel $data, array $context)
    {
        $address = $context["data"] ?? new AddressData();

        if ($address->get_lock()) {
            $address = new AddressData();
            $address->set_hidden(true);
        }
        $address->set_hidden(true);
        $address->set_type("sender");

        if ($data->isInitialized("addressName"))
            $address->set_address_name($data->getAddressName());
        if ($data->isInitialized("name"))
            $address->set_name($data->getName());
        if ($data->isInitialized("contact"))
            $address->set_contact($data->getContact());
        if ($data->isInitialized("mail"))
            $address->set_mail($data->getMail());
        if ($data->isInitialized("phone"))
            $address->set_phone($data->getPhone());
        if ($data->isInitialized("note"))
            $address->set_note($data->getNote());
        if ($data->isInitialized("street"))
            $address->set_street($data->getStreet());
        if ($data->isInitialized("city"))
            $address->set_city($data->getCity());
        if ($data->isInitialized("zip"))
            $address->set_zip($data->getZip());
        if ($data->isInitialized("country"))
            $address->set_country($data->getCountry());

        return $address;

    }

    private function CplCollectionAddressToCollectionAddressModel($data, string $type)
    {
        /**
         * @var EpsApiMyApi2WebModelsCustomerAddressModel $data
         */
        $collectionAddress = new CollectionAddressModel();
        $collectionAddress->setCity($data->getCity());
        $collectionAddress->setStreet($data->getStreet());
        $collectionAddress->setCountry($data->getCountry());
        $collectionAddress->setZip($data->getZipCode());
        $collectionAddress->setName(trim($data->getName() . ' ' . $data->getName2()));
        $collectionAddress->setCode(trim($data->getCode()));
        $collectionAddress->setDefault($data->getDefault());

        return $collectionAddress;

    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof AddressData && in_array($type, [ RecipientAddressModel::class, SenderAddressModel::class], true)
                || ($data instanceof RecipientAddressModel || $data instanceof  SenderAddressModel) && $type === AddressData:: class
                || $type === RecipientAddressModel::class && $data instanceof \WC_Order
                || $type === CollectionAddressModel::class && $data instanceof EpsApiMyApi2WebModelsCustomerAddressModel
            ;
    }
}