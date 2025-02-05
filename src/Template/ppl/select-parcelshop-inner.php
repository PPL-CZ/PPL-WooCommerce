<?php
defined("WPINC") or die();

$messages = [];
if (!$ageOk) {
    if ($parcelRequired)
        $messages[] = "Pro ověření věku je možné vybrat pouze obchod, ne výdejní box";
}
if ($parcelRequired && !$shipping_address)
    $messages[] = "Pro doručení zásilky je nutno vybrat výdejní místo";
$address = "";
if (wc()->cart && wc()->cart->get_customer()) {
    if (wc()->cart->get_customer()->has_shipping_address()) {
        $address = join(", ", array_filter([wc()->cart->get_customer()->get_shipping_address(), wc()->cart->get_customer()->get_shipping_postcode(), wc()->cart->get_customer()->get_shipping_city()]));
        $country = wc()->cart->get_customer()->get_shipping_country();
    }
    else {
        $address = join(", ", array_filter([wc()->cart->get_customer()->get_billing_address(), wc()->cart->get_customer()->get_billing_postcode(), wc()->cart->get_customer()->get_billing_address()]));
        $country = wc()->cart->get_customer()->get_billing_address();
    }
}
/**
 * @var \PPLCZ\Model\Model\ParcelDataModel $shipping_address
 */
if (isset($shipping_address) && $shipping_address) {
    $address =  join(', ', array_filter([$shipping_address->getStreet(), join(" ", array_filter([$shipping_address->getZipCode(), $shipping_address->getCity()]))]));
    $country = $shipping_address->getCountry();
}

?>
<div class="pplcz-parcelshop-inner" <?php echo ((isset($showMap) && $showMap) ? "data-pplcz-showmap='1'" : "") ?> >
    <div class="pplcz-select-parcelshop">
        <a href="#" class="pplcz-select-parcelshop" data-pplcz-select-parcel-shop="" <?php if (!$ageOk): ?> data-pplcz-parcelshop="1" <?php endif ?> data-address="<?php echo esc_html($address) ?>" data-country="<?php echo esc_html($country) ?>">
            <img src="<?php echo esc_url($image) ?>">
        </a>
    </div>
    <div class="pplcz-selected-parcelshop">
    <?php


    if (isset($shipping_address) && $shipping_address) {
        ?>
        <?php echo esc_html($shipping_address->getAccessPointType()); ?>  <a href="#" class="pplcz-clear-map" data-pplcz-select-parcel-shop="clear">
            [zrušit]
        </a>,
        <?php echo esc_html($shipping_address->getName()) ?>,
        <?php echo esc_html($shipping_address->getStreet()) ?>,
        <?php echo esc_html($shipping_address->getZipCode()) ?> <?php echo esc_html($shipping_address->getCity()) ?>

        <?php
    } else {
        ?>
        <?php if (!$parcelRequired): ?>
            Zboží bude doručeno na doručovací adresu nebo kliknutím na ikonu vyberte místo
        <?php else: ?>
            Kliknutím na ikonu vyberte místo
        <?php endif; ?>

    <?php } ?>
    <input type="hidden" name="pplcz_parcelshop" value='<?php echo esc_html($content) ?>'>
</div>