<?php
defined("WPINC") or die();

/**
    @var \PPLCZ\Model\Model\ParcelDataModel $shippingAddress
*/

if (!isset($shippingAddress))
    return;
?>
<address>
    <strong>Výdejní místo</strong><br/>
    <?php echo esc_html($shippingAddress->getAccessPointType());?><br/>
    <?php echo esc_html($shippingAddress->getName()) ?><br/>
    <?php echo esc_html($shippingAddress->getStreet()) ?><br/>
    <?php echo esc_html($shippingAddress->getZipCode()) ?> <?php echo esc_html($shippingAddress->getCity()) ?><br/>
</address>

