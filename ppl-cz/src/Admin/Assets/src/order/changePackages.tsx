
const createId = (orderId:number) => `#pplcz-order-panel-shipment-div-${orderId}-overlay`;

export const removeShipment = (nonce: string, orderId:number, shipmentId?:number) => {
    jQuery(createId(orderId)).find('button').attr("disabled", "disabled");
    // @ts-ignore
    wp.ajax.post({
        action: "pplcz_order_panel_remove_shipment",
        orderId,
        shipmentId,
        nonce
    }).done(function(response) {
        jQuery(createId(orderId)).html(response.html);
        jQuery(window).trigger(`pplcz-refresh-${orderId}`);
    }).fail(function(response) {
        jQuery(createId(orderId)).find('button').removeAttr("disabled");
    });
}

export const addPackage = (nonce: string, orderId:number, shipmentId?:number) => {
    jQuery(createId(orderId)).find('button').attr("disabled", "disabled");
    // @ts-ignore
    wp.ajax.post({
        action: "pplcz_order_panel_add_package",
        orderId,
        shipmentId,
        nonce
    }).done(function(response) {
        jQuery(createId(orderId)).html(response.html);
        jQuery(window).trigger(`pplcz-refresh-${orderId}`);
    }).fail(function(response) {
        jQuery(createId(orderId)).find('button').removeAttr("disabled");
    });
}

export const removePackage = (nonce: string, orderId:number, shipmentId?: number) => {
    jQuery(createId(orderId)).find('button').attr("disabled", "disabled");
    // @ts-ignore
    wp.ajax.post({
        action: "pplcz_order_panel_remove_package",
        orderId,
        shipmentId,
        nonce
    }).done(function(response) {
        jQuery(createId(orderId)).html(response.html);
        jQuery(window).trigger(`pplcz-refresh-${orderId}`);
    }).fail(function(response) {
        jQuery(createId(orderId)).find('button').removeAttr("disabled");
    });
}

export const cancelPackage =  (nonce: string, orderId:number, shipmentId:number, packageId: number) => {
    jQuery(`#pplcz-order-panel-shipment-div-${orderId}-overlay`).find('button').attr("disabled", "disabled");
    // @ts-ignore
    wp.ajax.post({
        action: "pplcz_order_panel_cancel_package",
        orderId,
        shipmentId,
        packageId,
        nonce
    }).done(function (response) {
        jQuery(createId(orderId)).html(response.html);
        jQuery(window).trigger(`pplcz-refresh-${orderId}`);
    }).fail(function (response) {
        jQuery(createId(orderId)).find('button').removeAttr("disabled");
        if (typeof response === "string") {
            // @ts-ignore
            wp.data.dispatch('core/notices').createNotice(
                'error', // Can be one of: success, info, warning, error.
                response, // Text string to display.
                {
                    isDismissible: true, // Whether the user can dismiss the notice.
                    // Any actions the user can perform.
                }
            );
        }
    });
}