

export const test_labels = (nonce: string, orderId: number, shipmentId: number) => {
    const id = `#pplcz-order-panel-shipment-div-${orderId}-overlay`;
    jQuery(id).find('button').attr("disabled", "disabled");
    // @ts-ignore
    wp.ajax.post({
        action: "pplcz_order_panel_test_labels",
        orderId,
        shipmentId,
        nonce
    }).done(function (response) {
        const hasLabels = !!jQuery(`${id} .refresh-shipments-labels`).length;
        jQuery(id).html(response.html);
        const afterHasLabels = !!jQuery(`${id} .refresh-shipments-labels`).length;
        if (hasLabels && !afterHasLabels) {
            const allLabels = jQuery(`${id} .all-labels`)[0];
            if (allLabels instanceof HTMLLinkElement) {
                document.location = allLabels.href;
            }
        }
        jQuery(window).trigger(`pplcz-refresh-${orderId}`);
    }).fail(function (response) {
        jQuery(id).find('button').removeAttr("disabled");

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
            jQuery(id).html(response.html);
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
    });
}

export const set_print_setting = (nonce: string, orderId: number, shipmentId: number, value: string, optionals: any) => {

    const id = `#pplcz-order-panel-shipment-div-${orderId}-overlay`;
    jQuery(id).find('button').attr("disabled", "disabled");
    // @ts-ignore
    const PPLczPlugin = window.PPLczPlugin = window.PPLczPlugin || [];
    PPLczPlugin.push(["wpUpdateStyle", `pplcz-order-panel-shipment-div-${orderId}-overlay`]);
    let unmount:any = null;
    let render: any= null;

    const item = jQuery("<div>").prependTo("body")[0];
    let response:any = null;

    const onFinish = function() {
        if (response) {
            unmount();
            jQuery(id).html(response);
            jQuery(window).trigger(`pplcz-refresh-${orderId}`);
        } else {
            unmount();
            // @ts-ignore
            wp.ajax.post({
                action: "pplcz_change_print",
                print: value,
                orderId,
                shipmentId,
                nonce
            }).done(function (response) {
                jQuery(id).html(response.html);
                jQuery(window).trigger(`pplcz-refresh-${orderId}`);
            });
        }
    }

    const onChange =  (newval: string) => {
        value = newval;
        render({
            optionals,
            value,
            onFinish,
            onChange,
        })
        response = null;
        // @ts-ignore
        wp.ajax.post({
            action: "pplcz_change_print",
            print: value,
            orderId,
            shipmentId,
            nonce
        }).done(function (resp) {
            response = resp.html;
        });
    }

    PPLczPlugin.push(["selectLabelPrint", item, {
        optionals,
        value,
        onFinish,
        onChange,
        "returnFunc": function(data) {
            unmount = data.unmount;
            render = data.render
        }
    }]);
}


export const create_labels2 = (nonce: string, orderId: number, shipment: any) => {
    const id = `#pplcz-order-panel-shipment-div-${orderId}-overlay`;
    jQuery(id).find('button').attr("disabled", "disabled");
    // @ts-ignore
    const PPLczPlugin = window.PPLczPlugin = window.PPLczPlugin || [];
    PPLczPlugin.push(["wpUpdateStyle", `pplcz-order-panel-shipment-div-${orderId}-overlay`]);
    let unmount:any = null;
    const item = jQuery("<div>").prependTo("body")[0];

    PPLczPlugin.push(["newLabel", item, {
        "hideOrderAnchor": false ,
        "shipment": shipment,
        "returnFunc": function(data) {
            unmount = data.unmount;
        },
        "onFinish": function() {
            // @ts-ignore
            wp.ajax.post({
                action: "pplcz_order_panel",
                orderId,
                nonce
            }).done(function (response) {
                unmount();
                jQuery(id).html(response.html);
                jQuery(window).trigger(`pplcz-refresh-${orderId}`);
            });
        }
    }]);
}

export const create_labels = (nonce: string, orderId: number, shipmentId?: number) => {
   const id = `#pplcz-order-panel-shipment-div-${orderId}-overlay`;
    jQuery(id).find('button').attr("disabled", "disabled");
    // @ts-ignore
    wp.ajax.post({
        action: "pplcz_order_panel_create_labels",
        orderId,
        shipmentId,
        nonce,
    }).done(function (response) {
        jQuery(id).html(response.html);
        jQuery(window).trigger(`pplcz-refresh-${orderId}`);
    }).fail(function(response) {
        jQuery(id).find('button').removeAttr("disabled");
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
            jQuery(id).html(response.html);
            jQuery(window).trigger(`pplcz-refresh-${orderId}`);
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
    });
}