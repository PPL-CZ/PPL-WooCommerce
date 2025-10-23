import {
    disableButtons,
    enableButtons,
    updateOverlay,
    showErrorNotice,
    ajaxPost
} from './utils';

/**
 * Testování stavu zásilky
 */
export const testShipmentState = (orderId: number, shipmentId?: number): void => {
    disableButtons(orderId);

    ajaxPost('pplcz_order_panel_test_labels', { orderId, shipmentId })
        .done((response: any) => {
            updateOverlay(orderId, response.html);
        })
        .fail((response: any) => {
            enableButtons(orderId);

            if (typeof response === 'string') {
                showErrorNotice(response);
            } else {
                if (response.html) {
                    updateOverlay(orderId, response.html);
                }
                if (response.message) {
                    showErrorNotice(response.message);
                }
            }
        });
};



