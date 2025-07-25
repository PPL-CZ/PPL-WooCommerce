import { components } from "../schema";

type ShipmentModel = components["schemas"]["ShipmentModel"];

const renderForm = (nonce: string, orderId: number, shipment: ShipmentModel) => {
    const id = `#pplcz-order-panel-shipment-div-${orderId}-overlay`;
    // @ts-ignore
    const PPLczPlugin = window.PPLczPlugin = window.PPLczPlugin || [];
    let unmount:any = null;
    const item = jQuery("<div>").prependTo("body")[0];

    PPLczPlugin.push(["newShipment", item, {
        "shipment": shipment,
        "returnFunc": function(data) {
            unmount = data.unmount;
        },
        "onChange": function() {
            // @ts-ignore
            wp.ajax.post({
                action: "pplcz_order_panel",
                orderId,
                nonce,
            }).done(function (response) {
                jQuery(id).html(response.html);
                jQuery(window).trigger(`pplcz-refresh-${orderId}`);
            });
        },
        "onFinish": function() {
            // @ts-ignore
            wp.ajax.post({
                action: "pplcz_order_panel",
                orderId,
                nonce,
            }).done(function (response) {
                unmount();
                jQuery(id).html(response.html);
                jQuery(window).trigger(`pplcz-refresh-${orderId}`);
            });
        }
    }]);
}

export const form = (nonce: string, orderId:number, shipment:ShipmentModel) => {
    const id = `#pplcz-order-panel-shipment-div-${orderId}-overlay`;
    if (shipment) {
        renderForm(nonce, orderId, shipment);
    }
    else {
        // @ts-ignore
        wp.ajax.post({
            action: "pplcz_order_panel_prepare_package",
            orderId,
            nonce
        }).done(function(response) {
            jQuery(id).html(response.html);
            renderForm(nonce, orderId, response.shipment);
        });
    }
}

