<?php
namespace PPLCZ\ModelNormalizer;

defined("WPINC") or die();

use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZ\Model\Model\ProductModel;
use PPLCZ\Serializer;

class ProductModelDenormalizer  implements DenormalizerInterface {

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \WC_Product && $type === ProductModel::class)
        {
            $data = $data->get_meta("_pplProductData");
            if (!is_array($data))
                return new ProductModel();
            return Serializer::getInstance()->denormalize($data, ProductModel::class);
        }
        else if ($data instanceof ProductModel && $type === \WC_Product::class)
        {
            if (!isset($context["product"]) || !($context['product'] instanceof \WC_Product)) {
                throw new \Exception("Undefined product");
            }
            $normalized = Serializer::getInstance()->normalize($data, "array");
            $context["product"]->add_meta_data("_pplProductData", $normalized, true) || $context['product']->update_meta_data("_pplProductData", $normalized);
            return  $context['product'];
        }
        throw new \Exception("Undefined denormalize");
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        if ($data instanceof \WC_Product && $type === ProductModel::class) {
            return true;
        } else if ($type === \WC_Product::class && $data instanceof ProductModel) {
            return true;
        }
        return false;
    }
}