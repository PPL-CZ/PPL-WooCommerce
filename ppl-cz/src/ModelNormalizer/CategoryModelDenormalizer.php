<?php
namespace PPLCZ\ModelNormalizer;

defined("WPINC") or die();

use PPLCZVendor\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLCZ\Model\Model\CategoryModel;
use PPLCZ\Serializer;


class CategoryModelDenormalizer  implements DenormalizerInterface {

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \WP_Term && $type === CategoryModel::class)
        {
            $data = get_term_meta($data->term_id, "_pplCategoryData", true);

            if (!is_array($data))
                return new CategoryModel();
            return Serializer::getInstance()->denormalize($data, CategoryModel::class);
        }
        else if ($data instanceof CategoryModel && $type === \WP_Term::class)
        {
            if (!isset($context["category"]) || !($context['category'] instanceof \WP_Term)) {
                throw new \Exception("Undefined category");
            }
            $term_id = $context['category']->term_id;
            $normalized = Serializer::getInstance()->normalize($data, "array");
            add_term_meta($term_id, "_pplCategoryData", $normalized, true) ||
                update_term_meta($term_id, "_pplCategoryData", $normalized);
            return  $context['category'];
        }
        throw new \Exception("Undefined denormalize");
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        if ($data instanceof \WP_Term && $type === CategoryModel::class) {
            return true;
        } else if ($type === \WP_Term::class && $data instanceof CategoryModel) {
            return true;
        }
        return false;
    }
}