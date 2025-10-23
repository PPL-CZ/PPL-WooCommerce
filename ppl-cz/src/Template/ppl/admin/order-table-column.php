<?php
defined("WPINC") or die();
?>
<?php
$shipmentPhase = [
    "Order" => "Objednáno",
    "InTransport" => "V&nbsp;přepravě",
    "Delivering" =>"Na&nbsp;cestě",
    "PickupPoint" => "Na&nbsp;výdejním&nbsp;místě",
    "Delivered" => "Doručeno",
    "Returning" => "Zpět&nbsp;k&nbsp;odesílateli",
    "BackToSender" => "Vráceno",
    //"Canceled" => "Zrušeno"
];

$forLabels = [];

foreach ($shipments as $key => $shipment) {
    if ($shipment->getImportState() === "None") {
        $forLabels[] = pplcz_normalize($shipment);
    }
}


?>
<div
        id="pplcz-order-<?php echo esc_html($order->get_id())?>-overlay"
        <?php if($forLabels): ?>
        data-shipments="<?php echo esc_html(wp_json_encode($forLabels)) ?>"
        data-orderId="<?php echo esc_html($order->get_id()) ?>"
        <?php endif; ?>
        class="pplcz-order-table-panel"
>
    <img src="<?php echo esc_html(pplcz_asset_icon("ppldhl_4084x598.png")); ?>" height="20"><br/>
    <?php
    /**
     * @var  \PPLCZ\Model\Model\ShipmentModel[] $shipments
     * @var \WC_Order $order
     */
    $pruchod = false;

    foreach ($shipments as $key => $shipment):
        $batchRemoteId = null;
        if ($shipment->getBatchRemoteId())
        {
            $batchRemoteId = $shipment->getBatchRemoteId();
        }

        $uri = $wcOrderUrl ?: '';

        if ($uri && $batchRemoteId) {
            if (strpos($uri, '?') !== false) {
                $uri .= "&pplcz_batch_remote_id=" . urlencode($batchRemoteId);
            } else {
                $uri .= "?pplcz_batch_remote_id=" . urlencode($batchRemoteId);
            }
        }
        else
        {
            $uri .= '#';
        }


?>
    <?php if ($shipment->getImportState() === "Complete") :?>
        <?php if ($batchRemoteId):?><a href="<?php echo esc_html($uri) ?>"  class="pplcz-batch-filter" >Hromadně</a> <a target="_blank" href="<?php echo esc_html(pplcz_get_download_pdf($shipment->getBatchRemoteId())) ?>" class="dashicons dashicons-admin-page"></a><br/><?php endif; ?>
    <?php foreach ($shipment->getPackages() as $package):?>
        <div style="white-space: nowrap;">
        <?php if ($package->getShipmentNumber()): ?><a href="https://www.ppl.cz/vyhledat-zasilku?shipmentId=<?php echo esc_html($package->getShipmentNumber())?>" target="_blank"><?php echo esc_html($package->getShipmentNumber()) ?><?php endif; ?>&nbsp;
        <?php if ($package->getLabelId() && in_array($package->getPhase(), ["Order", "None"])):?>
            <a target="_blank" href="<?php echo esc_html(pplcz_get_download_pdf($shipment->getBatchRemoteId(), $shipment->getId(), $package->getId())) ?>" class="dashicons dashicons-printer"></a>
        <?php endif; ?>
        <?php if ($package->isInitialized("phaseLabel")): ?>
        <?php echo esc_html(isset($shipmentPhase[$package->getPhase()]) ? $shipmentPhase[$package->getPhase()] : $package->getPhase()); ?>
        <?php endif; ?>
        </div>
    <?php endforeach;?>
    <?php else : ?>
    <?php if (!$jsShipmentsOk[$key] || $shipment->getImportErrors() || $shipment->getImportState() === "Error") :?><span style="color: red" class="dashicons dashicons-dismiss"></span>Chyba na zásilce<?php endif; ?>
    <?php endif; ?>
    <?php
    endforeach;
    ?>
</div>