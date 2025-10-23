import {addPackage, cancelPackage, removePackage, removeShipment} from "./changePackages";
import {form} from "./form";
import {create_labels, create_labels2, test_labels, set_print_setting, create_labels_add} from "./labels";

export const panel = (element) => {

    const orderId =  jQuery(element).data('orderid');

    const elementId = element.id;    const nonce = jQuery(element).data('nonce');

    jQuery(window).off(`pplcz-refresh-${orderId}`).on(`pplcz-refresh-${orderId}`, () => {
        panel(jQuery(`#${elementId}`)[0]);
    })
    jQuery(window).find(`pplcz-refresh-${orderId}`)

    const id =`#pplcz-order-panel-shipment-div-${orderId}-overlay`;

    jQuery(`${id} *`).off('click.pplcz-events').off("change.pplcz-events");

    jQuery(`${id} .pplcz_available_print_setting`).off("click.pplcz-event").on("click.pplcz-events", function(ev) {
        ev.preventDefault();

        const item = this;
        const optionals = jQuery(item).data("optionals");
        const value =  jQuery(item).data("value");
        const shipmentId = jQuery(item).data("shipmentid");
        set_print_setting(nonce, orderId, shipmentId, value, optionals);
    })



    jQuery(`${id} .add-package`).on('click.pplcz-events', (e)=> {
        e.preventDefault();
        const { orderid: orderId, shipmentid: shipmentId } = jQuery(e.currentTarget).data();
        addPackage(nonce, orderId, shipmentId);
    });

    jQuery(`${id} .remove-package`).on('click.pplcz-events', (e)=> {
        e.preventDefault();
        const { orderid: orderId, shipmentid: shipmentId } = jQuery(e.currentTarget).data();
        removePackage(nonce, orderId, shipmentId);
    });

    jQuery(`${id} .detail-shipment`).on('click.pplcz-events', (e)=> {
        e.preventDefault();
        const {orderid: orderId, shipment} = jQuery(e.currentTarget).data();
        form(nonce, orderId, shipment);
    });

    jQuery(`${id} .cancel-package`).on('click.pplcz-events', (e)=> {
        e.preventDefault();

        const {orderid: orderId, shipmentid: shipmentId, packageid: packageId } = jQuery(e.currentTarget).data();
        cancelPackage(nonce, orderId, shipmentId, packageId);
    });

    jQuery(`${id} .test-labels`).on('click.pplcz-events', (e)=> {
        e.preventDefault();
        const {orderid: orderId, shipmentid: shipmentId} = jQuery(e.currentTarget).data();
        create_labels(nonce, orderId, shipmentId);
    })

    jQuery(`${id} .create-labels`).on('click.pplcz-events', function(e) {
        e.preventDefault();
        const {orderid: orderId, shipment } = jQuery(e.currentTarget).data();
        create_labels2(nonce, orderId, shipment);
    });

    jQuery(`${id} .create-labels-add`).on('click.pplcz-events', function(e) {
        e.preventDefault();
        const {orderid: orderId, shipment } = jQuery(e.currentTarget).data();
        create_labels_add(nonce, orderId, shipment)

    });

    jQuery(`${id} .remove-shipment`).on('click.pplcz-events', (e)=> {
        e.preventDefault();
        const {orderid: orderId, shipmentid: shipmentId } = jQuery(e.currentTarget).data();
        removeShipment(nonce, orderId, shipmentId);
    })

    jQuery(`${id} .refresh-shipments`).on('click.pplcz-events', (e)=> {
        e.preventDefault();
        const {orderid: orderId, shipmentid: shipmentId} = jQuery(e.currentTarget).data();
        test_labels(nonce, orderId, shipmentId);
    })

    jQuery(`${id} .refresh-shipments-labels`).each(function (){
        const {orderid: orderId, shipmentid: shipmentId} = jQuery(this).data();
        setTimeout(()=> {
            test_labels(nonce, orderId, shipmentId);
        }, 2000);
    });
}