<?php
defined("WPINC") or die();
?>
<?php

/**
@var \PPLCZ\Model\Model\ParcelDataModel $shippingAddress
 */

?>
<div id="pplcz_parcelshop_<?php echo esc_html($meta_id) ?>" class="pplcz_parcelshop_orderitems" style="display: <?php echo $show ? "block": "none" ?>;border: 1px solid gray; margin-top: 0.5em; margin-bottom: 0.5em; padding: 0.5em ">
    <?php if ($shippingAddress): ?>
    <?php
    $podminky = join(", ", array_filter([
        "Typ ". esc_html($shippingAddress->getAccessPointType()),
        $shippingAddress->getActiveCardPayment() ? "Platba kartou" : null,
        $shippingAddress->getActiveCashPayment() ? "Hotově" : null,
    ]));
    ?>
    <?php echo esc_html($shippingAddress->getName()) ?>,
    <?php echo esc_html($shippingAddress->getStreet()) ?>,
    <?php echo esc_html($shippingAddress->getZipCode()) ?> <?php echo esc_html($shippingAddress->getCity()) ?><br/>
    <?php echo esc_html($podminky) ?><?php if ($podminky): ?><br/><?php endif;?> <br/>
        <button style="display:none" class="pplcz_parcelshop_<?php echo esc_html($meta_id) ?> pplcz_parcelshop_parcelshop button">Nastavit jiný obchod</button>
        <button style="display:none" class="pplcz_parcelshop_<?php echo esc_html($meta_id) ?> pplcz_parcelshop_parcelbox button">Nastavit jiný box</button>
        <button style="display:none" class="pplcz_parcelshop_<?php echo esc_html($meta_id) ?> pplcz_parcelshop_clear button">Zrušit parcelshop/parcelbox</button>
    <?php else:?>
    Zásilka bude směřována na doručovací adresu v objednávce<br/>
        <button style="display:none" class="pplcz_parcelshop_<?php echo esc_html($meta_id) ?> pplcz_parcelshop_parcelshop button">Nastavit parcelshop</button>
<?php endif; ?>
    <input type="hidden"
           data-meta_id="<?php echo esc_html($meta_id); ?>"
           data-order_id="<?php echo esc_html($order_id); ?>"
           data-nonce="<?php echo esc_html($nonce) ?>"
           name="pplcz_parcelshop[<?php echo esc_html($meta_id) ?>]"
           value='<?php echo esc_html($hidden_data) ?>'>
</div>
