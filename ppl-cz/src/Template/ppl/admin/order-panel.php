<?php
// phpcs:ignoreFile WordPress.Security.EscapeOutput.OutputNotEscaped

use PPLCZ\Model\Model\LabelPrintModel;

defined("WPINC") or die();
$pplcz_orderId_safe = (int)$order->get_id();
?>
<div id="pplcz-order-panel-shipment-div-<?php echo $pplcz_orderId_safe ?>-overlay"
     data-orderId='<?php echo $pplcz_orderId_safe ?>'
     data-nonce='<?php echo esc_html($nonce) ?>'
>
<?php
if ($shipments) {
/**
 * @var  \PPLCZ\Model\Model\ShipmentModel[] $shipments
 * @var \WC_Order $order
 */

foreach ($shipments as $key => $shipment):
    if ($shipment->isInitialized("id"))
        $shipmentId = $shipment->getId();
    else
        $shipmentId = 0;
    $addId = $order->get_id() . "_" . time();
    if ($shipmentId)
        $addId = $shipmentId;

    if ($shipment->isInitialized("recipient")) {
        /**
         * @var \PPLCZ\Model\Model\RecipientAddressModel $recipient
         */
        $recipient = $shipment->getRecipient();
        $recipient = join(', ', array_filter(array_map(function ($item) use ($recipient) {
            if ($item === "city") {
                $output = [];
                if ($recipient->isInitialized("zip"))
                    $output[] = $recipient->getZip();
                if ($recipient->isInitialized("city"))
                    $output[] = $recipient->getCity();
                return join(' ', $output);
            }
            if ($recipient->isInitialized($item))
                return $recipient->{"get{$item}"}();
            return null;
        }, ["name", "contact", "street", "city", "country", "phone"])));
    } else {
        $recipient = null;
    }
    $packages = "0";
    if ($shipment->isInitialized("packages")) {
        $packages = $shipment->getPackages();
        $packages = count($packages);
    }
?>
<table>
    <tr>
        <td style="text-align: right; padding-right: 1em">Služba:</td>
        <td style="vertical-align: middle">
            <?php if ($shipment->isInitialized("serviceCode")): ?>
            <?php echo esc_html($shipment->getServiceName() . " (" . $shipment->getServiceCode() . ")"); ?>&nbsp;<img src="<?php echo esc_html($jsImage[$key]) ?>" height="20" style="position: relative; top: 5px"  />
            <?php else: ?>
            Nespecifikovaná služba
            <?php endif; ?>
        </td>

    </tr>
    <tr>
        <td style="text-align: right; padding-right: 1em">Adresa:</td>
        <td>
<?php if ($recipient): ?>
    <address>
<?php echo esc_html($recipient) ?><br/>
    </address>
<?php endif; ?>
            <?php if ($shipment->isInitialized("hasParcel") && $shipment->getHasParcel()
                && $shipment->isInitialized("parcel") && $shipment->getParcel()) :
                $parcel = $shipment->getParcel();
                $parcelAddress = join(', ', array_filter(array_map(function ($item) use ($parcel) {
                    if ($item === "city") {
                        $output = [];
                        if ($parcel->isInitialized("zip"))
                            $output[] = $parcel->getZip();
                        if ($parcel->isInitialized("city"))
                            $output[] = $parcel->getCity();
                        return join(' ', $output);
                    }
                    if ($parcel->isInitialized($item))
                        return $parcel->{"get{$item}"}();

                    return null;
                }, ["name", "name2", "street", "city", "country"])));

                ?>
                Zásilka(y) jsou určeny na <?php if (strtolower($parcel->getType()) == "parcelshop"):?> parcelshop <?php else: ?>parcelbox<?php endif; ?>:
                <address>
                    <?php echo esc_html($parcelAddress) ?>
                </address>
            <?php endif; ?>
        </td>
    </tr>
    <?php if ($shipment->isInitialized("note") && $shipment->getNote()): ?>
    <tr>
       <td style="text-align: right; padding-right: 1em">Poznámka:</td><td><?php echo esc_html($shipment->getNote());?></td>
    </tr>
    <?php endif; ?>
        <?php if ($shipment->isInitialized("codValue") && $shipment->getCodValue()): ?>
    <tr>
        <td style="padding-right: 1em; text-align: right">
            Dobírka:
        </td>
        <td>
            <?php echo esc_html($shipment->getCodValue()) ?><?php echo $shipment->isInitialized("codValueCurrency") ? esc_html($shipment->getCodValueCurrency()) : "???"?>
        </td>
    </tr>
        <tr>
            <td style="padding-right: 1em; text-align: right" >Variabilní symbol:</td>
            <td><?php echo $shipment->isInitialized("codVariableNumber") ? esc_html($shipment->getCodVariableNumber()) : "Bez variabilního čísla"; ?></td>
        </tr>
    <?php endif; ?>
    <?php if (!$shipment->getImportState() || $shipment->getImportState() === 'None' || $shipment->getImportState() === "Error"): ?>
    <tr>
        <td style="text-align: right; padding-right: 1em">Počet zásilek:</td>
        <td>
            <?php echo esc_html($packages); ?>
            <button class="add-package"
                    data-orderId="<?php echo esc_html($order->get_id()) ?>"
                    <?php if ($shipment->isInitialized("id")): ?>data-shipmentId="<?php echo esc_html($shipment->getId()) ?>" <?php endif; ?> ><span class="dashicons dashicons-plus"></span>Přidat balík</button>
            <?php if ($packages > 1): ?>
            <button class="remove-package"
                    data-orderId="<?php echo esc_html($order->get_id())?>"
                    <?php if ($shipment->isInitialized("id")): ?>data-shipmentId="<?php echo esc_html($shipment->getId()) ?>" <?php endif; ?> ><span class="dashicons dashicons-minus"></span>Odebrat balík</button>
            <?php endif;?>
        </td>
    </tr>
    <?php endif; ?>
    <?php if ($jsShipmentsErrors[$key] && (!$shipment->getImportState() || $shipment->getImportState() === 'None' || $shipment->getImportState() === "Error") ): ?>
        <tr>
            <td style="text-align: right; padding-right: 1em">Chyby na zásilce</td>
            <td>
                <div class="notice notice-error inline">
                    <?php
                    foreach ($jsShipmentsErrors[$key] as  $key2=> $item) {
                        ?><p><?php echo esc_html(join(",", $item)); ?></p><?php
                    }
                    ?>
                </div>
            </td>
        </tr>
    <?php endif;?>
<?php if ($shipment->getImportErrors()): ?>
<tr>
    <td style="text-align: right; padding-right: 1em">Chyby při importu</td>
    <td>
    <div class="notice notice-error inline">
        <?php foreach ($shipment->getImportErrors() as $importError): ?>
            <p><?php echo esc_html($importError) ?></p>
        <?php endforeach; ?>
        <?php
            foreach ($jsShipmentsErrors[$key] as  $key2=> $item) {
            ?><p><?php echo esc_html(join(",", $item)); ?></p><?php
            }
        ?>
    </div>
    </td>
</tr>
<?php endif; ?>
</table>

<?php if ($shipment->getImportState() === "Complete" || array_filter($shipment->getPackages(),function ($item){
   return $item->getImportError();
})) :?>
<table>
    <?php if ($shipment->getBatchRemoteId()):?>
    <tr>
        <td>
            Všechny zásilky na objednávce
        </td>
        <td style="vertical-align: center">
            <a id="ppl_reference_<?php echo urlencode($shipment->getReferenceId()); ?>" class="button all-labels pplcz-label-download" target="_blank" href="<?php echo esc_html(pplcz_get_download_pdf($shipment->getBatchRemoteId(), $shipment->getId(), null,$shipment->getPrintState() ?: $selectedPrint)) ?>" >
                <span style="position: relative; top: 5px" class="dashicons dashicons-admin-page"></span>
            </a>
            <?php
                $shipmentSelectedPrint = $shipment->getPrintState() ?: $selectedPrint;
                $printLabel = array_filter($availablePrinters, function (LabelPrintModel $item) use ($shipmentSelectedPrint) {
                    return $item->getCode() === $shipmentSelectedPrint;
                });
                if ($printLabel):
                    $printLabel = reset($printLabel);
?>
                    <span style="position: relative; top: 5px">
                        <?php echo esc_html($printLabel->getTitle()) ?>
                    </span>
                    <a style="position: relative; top: 5px"
                       class="pplcz_available_print_setting"
                       href="#" data-optionals='<?php echo esc_html(wp_json_encode(pplcz_normalize($availablePrinters))) ?>'
                       data-value="<?php echo esc_html($shipmentSelectedPrint) ?>"
                       data-shipmentid="<?php echo esc_html($shipmentId) ?>"
                    >Změnit</a>
                <?php
                endif;
            ?>
        </td>
    </tr>
    <?php endif ?>
<?php foreach ($shipment->getPackages() as $packageKey => $package):
    $packageKey++;
    ?>
    <tr>
        <td>
            <?php if ($package->getShipmentNumber()) : ?>
            <a href="https://www.ppl.cz/vyhledat-zasilku?shipmentId=<?php echo  esc_html($package->getShipmentNumber()) ?>"><?php echo esc_html($package->getShipmentNumber())?></a>
            <?php endif; ?>
            <?php if ($package->getReferenceId()) : ?>
            <?php echo esc_html("(ref: " . $package->getReferenceId() . ")") ?>
            <?php endif; ?>
        </td>
        <td style="vertical-align: middle;  line-height: 2.5em">
            <?php if ($package->getLabelId()):?>
                <?php if ($package->getPhase() === "None" || $package->getPhase() === "Order"): ?>
                <a id="pplcz-order-panel-anchor-href-<?php echo esc_html($addId) ?>"  target="_blank" href="<?php echo esc_html(pplcz_get_download_pdf($shipment->getBatchRemoteId(), $shipment->getId(), $package->getId(), $shipment->getPrintState() ?: $selectedPrint)); ?>" class="button pplcz-label-download">
                    <span style="position: relative; top: 5px" class="dashicons dashicons-printer"></span>
                </a>
                <button class="button cancel-package"
                        data-orderId="<?php echo esc_html($order->get_id())?>"
                        data-shipmentId="<?php echo esc_html($shipment->getId()) ?>"
                        data-packageId="<?php echo esc_html($package->getId()) ?>">Zrušit tuto zásilku</button>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($package->isInitialized("phase") && $package->getPhase() === "Canceled"): ?>
            Zrušeno
            <?php else: ?>
            <?php echo esc_html($package->getPhaseLabel()); ?>
            <?php endif; ?></td>
        <td style="color: red"><?php echo esc_html($package->getImportError()) ?></td>
        <td style="color: red"><?php echo esc_html($package->getImportErrorCode()) ?></td>
    </tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<hr/>
<?php if (!$shipment->getImportState() || $shipment->getImportState() === 'None' || $shipment->getImportState() === "Error"): ?>
<button class="button detail-shipment"
        data-orderId="<?php echo esc_html($order->get_id())?>"
        <?php if ($shipment->isInitialized("id")): ?>data-shipment='<?php echo esc_html(wp_json_encode(\PPLCZ\Serializer::getInstance()->normalize($shipment))) ?>'<?php endif; ?> >Upravit zásilku</button>
<?php endif; ?>
<?php if (in_array($shipment->getImportState(), ["InProcess", "InProgress", "Accepted"])): ?>
    <button disabled
            class="button refresh-shipments-labels"
            data-orderId="<?php echo esc_html($order->get_id())?>"
            <?php if ($shipment->isInitialized("id")): ?>data-shipmentId="<?php echo esc_html($shipment->getId()) ?>" <?php endif; ?>>Probíhá příprava etikety</button>
<?php elseif($shipment->getImportState() === "Complete"): ?>
    <button class="button refresh-shipments"
            type="button"
            data-orderId="<?php echo esc_html($order->get_id())?>"
            <?php if ($shipment->isInitialized("id")): ?>data-shipmentId="<?php echo esc_html($shipment->getId()) ?>" <?php endif; ?>>Aktualizovat stav zásilky</button>
<?php elseif  ($jsShipmentsOk[$key] || $shipment->getImportState() === "Error"): ?>
<button class="button create-labels"
        type="button"
        data-orderId="<?php echo  esc_html($order->get_id())?>"
        <?php if ($shipment->getId()): ?>data-shipmentId="<?php echo esc_html($shipment->getId()) ?>"  <?php endif; ?> data-shipment='<?php echo esc_html(wp_json_encode(pplcz_normalize($shipment))) ?>'>Tisk etiket</button>
    <button class="button create-labels-add"
            type="button"
            data-orderId="<?php echo  esc_html($order->get_id())?>"
        <?php if ($shipment->getId()): ?>data-shipmentId="<?php echo esc_html($shipment->getId()) ?>"  <?php endif; ?> data-shipment='<?php echo esc_html(wp_json_encode(pplcz_normalize($shipment))) ?>'>Do hromadného tisku etiket <?php if ($shipment->getBatchId()) :?>(#<?php echo esc_html($shipment->getBatchId()) ?>)<?php endif; ?></button>
<?php endif; ?>
    <?php
    if ($shipment->getId() && in_array($shipment->getImportState(), [
            "Error",
            "None",
            ""
        ], true)) {
        ?>
        <button class="button remove-shipment"
                type="button"
                data-orderId="<?php echo esc_html($order->get_id())?>"
                data-shipmentId="<?php echo esc_html($shipment->getId()) ?>">Odstranit</button>
    <?php } ?>
    <?php if ($batchs[$key]): ?>
        <a class="button" href="<?php echo esc_html($batchs[$key]); ?>">Tisková dávka</a>
    <?php endif; ?>
    <?php if ($key !=  array_key_last($shipments)) :?>
    <hr style="margin-left: -12px; margin-right:-12px"/>
    <?php endif; ?>
    <?php
endforeach;
?>

    <?php if (!array_filter($shipments, function ($item) {
        return !$item->getId();
    })): ?>
    <hr style="margin-left: -12px; margin-right:-12px"/>
    Chcete vytvořit další dopravu v rámci PPL?
    <br/><button data-orderId="<?php echo esc_html($order->get_id())?>" class="button detail-shipment">Vytvořit</button>
    <?php endif; ?>
<?php
} else {
?>
    Pro tuto objednávku nebyla vybrána doprava pomoci PPL.<br/><button data-orderId="<?php echo esc_html($order->get_id())?>" class="button detail-shipment">Chcete dopravu vytvořit?</button>
<?php
}
?>
</div>