<?php

namespace PPLCZ\ModelNormalizer;


class CartDataModelNormalizer extends  \PPLCZ\Model\Normalizer\CartDataModelNormalizer
{
    /**
     * @return mixed
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $nulldata=[];
        foreach ($data as $key => $item)
        {
            if ($item === null) {
                $nulldata[] = $key;
                unset($data[$key]);
            }
        }

        $object = parent::denormalize($data, $class, $format, $context);
        foreach ($nulldata as $key) {
            $method = "set$key";
            $object->$method(null);
        }
        return $object;
    }

    public function getSupportedTypes(?string $format = null): ?array
    {
        return array('PPLCZ\\Model\\Model\\CartDataModel' => false);
    }
}