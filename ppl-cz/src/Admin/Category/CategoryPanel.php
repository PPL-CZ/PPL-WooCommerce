<?php

namespace PPLCZ\Admin\Category;

use PPLCZ\Admin\Assets\JsTemplate;
use PPLCZ\Model\Model\CategoryModel;
use PPLCZ\Model\Model\PackageSizeModel;
use PPLCZ\Model\Model\ShipmentMethodModel;
use PPLCZ\Serializer;
use PPLCZ\Setting\MethodSetting;
use PPLCZ\ShipmentMethod;

class CategoryPanel {

    public static function get_shipping()
    {
        $output = [];
        foreach (MethodSetting::getMethods() as $v) {
            $output[] = pplcz_normalize($v);
        }
        return $output;
    }

    public static function register()
    {
        //add_action('product_cat_edit_form_fields', [self::class, "render"], 10, 2);
        add_action('product_cat_add_form_fields', [self::class, "render"], 10);
        add_action('product_cat_edit_form_fields', [self::class, "render"], 10, 2);
        add_action('edited_product_cat', [self::class, "save"], 10, 2);
        add_action('created_product_cat', [self::class, "save"], 10, 2);
    }

    public static function save($term_id, $tt_id)
    {
        if (!isset($_POST['pplNonce']) || !wp_verify_nonce(sanitize_key($_POST['pplNonce']), 'category'))
        {
            return;
        }
        $pplDisabledParcelBox = false;
        $pplDisabledParcelShop = false;
        $pplDisabledAlzaBox = false;
        $pplDisabledTransport = [];
        $pplConfirmAge15 = false;
        $pplConfirmAge18 = false;



        $xSize = null;
        $ySize = null;
        $zSize = null;
        $weight = null;

        if (isset($_POST['pplSize_xSize'])) {
            $xSize = intval(sanitize_post(wp_unslash($_POST['pplSize_xSize']), 'raw'));
            if ($xSize <= 0)
                $xSize = null;
        }

        if (isset($_POST['pplSize_ySize'])) {
            $ySize = floatval(sanitize_post(wp_unslash($_POST['pplSize_ySize']), 'raw'));
            if ($ySize <= 0)
                $ySize = null;
        }

        if (isset($_POST['pplSize_zSize'])) {
            $zSize = floatval(sanitize_post(wp_unslash($_POST['pplSize_zSize']), 'raw'));
            if ($zSize <= 0)
                $zSize = null;
        }

        if (isset($_POST['pplConfirmAge15']))
            $pplConfirmAge15 = sanitize_post(wp_unslash($_POST['pplConfirmAge15']), 'raw');
        if (isset($_POST['pplConfirmAge18']))
            $pplConfirmAge18 = sanitize_post(wp_unslash($_POST['pplConfirmAge18']), 'raw');

        if (isset($_POST['pplDisabledTransport']) && is_array($_POST['pplDisabledTransport'])) {
            $pplDisabledTransport = map_deep(wp_unslash($_POST['pplDisabledTransport']), 'sanitize_text_field');
        }

        $pplDisabledTransport = wc_clean($pplDisabledTransport);

        if (isset($_POST['pplDisabledParcelBox']))
            $pplDisabledParcelBox = sanitize_post(wp_unslash($_POST['pplDisabledParcelBox']), 'raw');

        if (isset($_POST['pplDisabledParcelShop']))
            $pplDisabledParcelShop = sanitize_post(wp_unslash($_POST['pplDisabledParcelShop']), 'raw');

        if (isset($_POST['pplDisabledAlzaBox']))
            $pplDisabledAlzaBox = sanitize_post(wp_unslash($_POST['pplDisabledAlzaBox']), 'raw');

        $model = new CategoryModel();

        if ($xSize || $ySize || $zSize) {
            $size = new PackageSizeModel();
            $size->setXSize($xSize);
            $size->setYSize($ySize);
            $size->setZSize($zSize);
            $model->setPplSize($size);
        }

        $model->setPplConfirmAge15(!!$pplConfirmAge15);
        $model->setPplConfirmAge18(!!$pplConfirmAge18);

        if (is_array($pplDisabledTransport)) {
            $model->setPplDisabledTransport($pplDisabledTransport);
        }

        $model->setPplDisabledParcelBox($pplDisabledParcelBox);
        $model->setPplDisabledParcelShop($pplDisabledParcelShop);
        $model->setPplDisabledAlzaBox($pplDisabledAlzaBox);

        $category = get_term($term_id);
        pplcz_denormalize($model, \WP_Term::class, [ "category" => $category]);
        pplcz_set_update_setting();
    }

    private static function renderForm($categoryModel, $asTd)
    {
        $shipments = self::get_shipping();
?>
        <div id="pplcz_tab"
                         data-pplNonce='<?php echo esc_html(wp_create_nonce("category")) ?>'
                         data-data='<?php echo esc_html(wp_json_encode(pplcz_normalize($categoryModel))) ?>'
                         data-methods='<?php echo esc_html(wp_json_encode($shipments)) ?>'>
        </div>
<?php
        JsTemplate::add_inline_script("pplczInitCategoryTab", "pplcz_tab");
    }

    public static function render($taxonomy = null)
    {
            $categoryModel = new CategoryModel();

            if ($taxonomy instanceof \WP_Term)
                $categoryModel = Serializer::getInstance()->denormalize($taxonomy, CategoryModel::class);

            if ($taxonomy === "product_cat")
            {
                echo "PPL";
                self::renderForm($categoryModel, false);
            } else {
            ?>
            <tr class="form-field">
                <th>
                    PPL
                </th>
                <td>
                <?php self::renderForm($categoryModel, true); ?>
                </td>
            </tr>
            <?php
            }
    }
}