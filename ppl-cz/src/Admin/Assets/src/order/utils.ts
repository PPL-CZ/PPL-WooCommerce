/**
 * Společné utility pro order modul
 */

/**
 * Vytvoří ID overlay elementu pro danou objednávku
 */
export const createOverlayId = (orderId: number): string =>
    `#pplcz-order-panel-shipment-div-${orderId}-overlay`;

/**
 * Vypíše error notifikaci přes WordPress notices
 */
export const showErrorNotice = (message: string): void => {
    // @ts-ignore
    wp.data.dispatch('core/notices').createNotice(
        'error',
        message,
        { isDismissible: true }
    );
};

/**
 * Zakáže všechna tlačítka v rámci overlay
 */
export const disableButtons = (orderId: number): void => {
    jQuery(createOverlayId(orderId)).find('button').attr('disabled', 'disabled');
};

/**
 * Povolí všechna tlačítka v rámci overlay
 */
export const enableButtons = (orderId: number): void => {
    jQuery(createOverlayId(orderId)).find('button').removeAttr('disabled');
};

/**
 * Aktualizuje HTML obsah overlay a spustí refresh event
 */
export const updateOverlay = (orderId: number, html: string): void => {
    jQuery(createOverlayId(orderId)).html(html);
    jQuery(window).trigger(`pplcz-refresh-${orderId}`);
};

/**
 * Spustí AJAX požadavek na WordPress backend
 */
export const ajaxPost = (action: string, data: Record<string, any>): JQuery.jqXHR => {
    // @ts-ignore
    return wp.ajax.post({ action, ...data });
};

/**
 * Univerzální handler pro akce balíčků
 */
export const handlePackageAction = (
    action: string,
    nonce: string,
    orderId: number,
    shipmentId: number|undefined = undefined,
    packageId: number|undefined = undefined
): void => {
    disableButtons(orderId);

    ajaxPost(action, { orderId, shipmentId, packageId, nonce })
        .done((response: any) => {
            updateOverlay(orderId, response.html);
        })
        .fail((response: any) => {
            enableButtons(orderId);

            if (typeof response === 'string') {
                showErrorNotice(response);
            } else if (response?.html) {
                updateOverlay(orderId, response.html);
                if (response.message) {
                    showErrorNotice(response.message);
                }
            }
        });
};
