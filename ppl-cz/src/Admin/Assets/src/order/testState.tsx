export const testShipmentState = (orderId: number, shipmentId?: number) => {
    const jQueryId = `#pplcz-order-panel-shipment-div-${orderId}-overlay`;

    jQuery(jQueryId).find('button').attr('disabled', "disabled");
    // @ts-ignore
    wp.ajax.post({
        action: "pplcz_order_panel_test_labels",
        orderId,
        shipmentId,
    }).done((response)=>{
        jQuery(jQueryId).html(response.html);
        jQuery(window).trigger(`pplcz-refresh-${orderId}`);
    }).fail((response)=>{
        jQuery(jQueryId).find('button').removeAttr("disabled");
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
        else {
            jQuery(jQueryId).html(response.html);
            // @ts-ignore
            wp.data.dispatch('core/notices').createNotice(
                'error', // Can be one of: success, info, warning, error.
                response.message, // Text string to display.
                {
                    isDismissible: true, // Whether the user can dismiss the notice.
                    // Any actions the user can perform.
                }
            );
        }
    })
}



