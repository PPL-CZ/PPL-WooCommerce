<?php
namespace PPLCZ\ModelNormalizer;

use PPLCZ\Data\BatchData;
use PPLCZ\Model\Model\BatchModel;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class BatchModelDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof BatchData) {
            $batch = new BatchModel();
            $batch->setId($data->get_id());
            $batch->setCreated($data->get_created_at());
            $batch->setLock($data->get_lock());
            $batch->setRemoteBatchId($data->get_remote_batch_id());
            return $batch;
        }

        return null;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof BatchData && $type === BatchModel::class;
    }
}