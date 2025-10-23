<?php
namespace PPLCZ\ModelNormalizer;

use PPLCZ\Model\Model\WpErrorModel;
use PPLCZ\TLoader;
use PPLCZ\Validator\WP_Error;
use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class WpErrorModelDenormalizer implements DenormalizerInterface
{


    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        /**
         * @var \WP_Error $data
         */
        $output = [];
        foreach ($data->errors as $key =>$values)
        {
            $errorModel = new WpErrorModel();
            $errorModel->setKey($key);
            $errorModel->setValues($values);
            $output[] = $errorModel;
        }
        return $output;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof \WP_Error && $type === WpErrorModel::class. "[]";

    }
}