<?php
defined("WPINC") or die();


return call_user_func(function() {

    $address = null;
    try {
        $address = get_transient(pplcz_create_name("collection_address")) ?: null;
        if (!is_array($address))
            $address = null;
    }
    catch (Exception $ex)
    {

    }

    try {
        if (!$address || defined("PPLCZ_REFRESH")) {


            $cpl = new \PPLCZ\Admin\CPLOperation();
            $collectionAddresses = $cpl->getCollectionAddresses();

            foreach ($collectionAddresses as $key => $value) {
                $collectionAddresses[$key] = \PPLCZ\Serializer::getInstance()->denormalize($value, \PPLCZ\Model\Model\CollectionAddressModel::class);
            }

            if ($collectionAddresses) {
                $collectionAddress = array_filter($collectionAddresses, function (\PPLCZ\Model\Model\CollectionAddressModel $model) {
                    return $model->getCode() === "PICK";
                });
                if ($collectionAddress) {
                    $address = reset($collectionAddress);
                    set_transient(pplcz_create_name("collection_address"), \PPLCZ\Serializer::getInstance()->normalize($address));
                    return  \PPLCZ\Serializer::getInstance()->normalize($address);
                }
            }
        }
    }
    catch (\Exception $exception)
    {
        return null;
    }
    return $address;
});