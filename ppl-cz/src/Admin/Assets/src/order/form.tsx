import { components } from "../schema";
import { updateOverlay, ajaxPost } from './utils';

type ShipmentModel = components["schemas"]["ShipmentModel"];

/**
 * Zobrazení formuláře pro úpravu zásilky
 */
const renderShipmentForm = (nonce: string, orderId: number, shipment: ShipmentModel): void => {
    // @ts-ignore
    const PPLczPlugin = window.PPLczPlugin = window.PPLczPlugin || [];
    const containerEl = jQuery("<div>").prependTo("body")[0];
    let unmount: any = null;

    const refreshPanel = (): Promise<any> =>
        ajaxPost('pplcz_order_panel', { orderId, nonce });

    PPLczPlugin.push(["newShipment", containerEl, {
        shipment,
        returnFunc: (data: any) => {
            unmount = data.unmount;
        },
        onChange: () => {
            refreshPanel().done((response: any) => {
                updateOverlay(orderId, response.html);
            });
        },
        onFinish: () => {
            refreshPanel().done((response: any) => {
                unmount();
                updateOverlay(orderId, response.html);
            });
        }
    }]);
};

/**
 * Zobrazí formulář pro úpravu zásilky (pokud neexistuje, připraví novou)
 */
export const form = (nonce: string, orderId: number, shipment?: ShipmentModel): void => {
    if (shipment) {
        renderShipmentForm(nonce, orderId, shipment);
    } else {
        ajaxPost('pplcz_order_panel_prepare_package', { orderId, nonce })
            .done((response: any) => {
                updateOverlay(orderId, response.html);
                renderShipmentForm(nonce, orderId, response.shipment);
            });
    }
};

