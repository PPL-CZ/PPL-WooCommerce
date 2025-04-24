<?php

namespace PPLCZ\ModelNormalizer;

defined("WPINC") or die();


use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZ\Data\CollectionData;
use PPLCZ\Model\Model\CollectionModel;
use PPLCZ\Model\Model\NewCollectionModel;


class CollectionDataDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof CollectionData && $type == CollectionModel::class) {
            $collection = new CollectionModel();
            $collection->setId($data->get_id());
            $collection->setShipmentCount($data->get_shipment_count());
            $collection->setEstimatedShipmentCount($data->get_estimated_shipment_count());
            $collection->setCreatedDate($data->get_created_date());

            if ($data->get_send_date())
                $collection->setSendDate($data->get_send_date());
            if ($data->get_send_to_api_date())
                $collection->setSendToApiDate($data->get_send_to_api_date());

            if ($data->get_reference_id())
                $collection->setReferenceId($data->get_reference_id());
            if ($data->get_state())
                $collection->setState($data->get_state());
            if ($data->get_email())
                $collection->setEmail($data->get_email());
            if ($data->get_telephone())
                $collection->setTelephone($data->get_telephone());
            if ($data->get_contact())
                $collection->setContact($data->get_contact());
            if ($data->get_remote_collection_id())
                $collection->setRemoteCollectionId($data->get_remote_collection_id());

            return $collection;
        }
        else if ($data instanceof NewCollectionModel && $type == CollectionData::class)
        {
            $collection = new CollectionData();

            $collection->set_send_date($data->getSendDate());
            $collection->set_estimated_shipment_count($data->getEstimatedShipmentCount());
            if ($data->isInitialized("note"))
                $collection->set_note($data->getNote());

            if ($data->isInitialized("contact"))
                $collection->set_contact($data->getContact());

            if ($data->isInitialized("telephone"))
                $collection->set_telephone($data->getTelephone());
            if ($data->isInitialized("email"))
                $collection->set_email($data->getEmail());
            return $collection;
        }

        throw new \Exception("Unsupported denormalize");
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof CollectionData && $type == CollectionModel::class ||
               $data instanceof NewCollectionModel && $type == CollectionData::class;

    }
}
