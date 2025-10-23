import {
    createOverlayId,
    disableButtons,
    enableButtons,
    updateOverlay,
    showErrorNotice,
    ajaxPost
} from './utils';

/**
 * Testování/refresh štítků zásilky
 */
export const test_labels = (nonce: string, orderId: number, shipmentId: number): void => {
    const id = createOverlayId(orderId);
    disableButtons(orderId);

    ajaxPost('pplcz_order_panel_test_labels', { orderId, shipmentId, nonce })
        .done((response: any) => {
            const hadLabels = !!jQuery(`${id} .refresh-shipments-labels`).length;
            updateOverlay(orderId, response.html);
            const hasLabels = !!jQuery(`${id} .refresh-shipments-labels`).length;

            // Automatické přesměrování na stažení všech štítků
            if (hadLabels && !hasLabels) {
                const allLabelsLink = jQuery(`${id} .all-labels`)[0];
                if (allLabelsLink instanceof HTMLLinkElement) {
                    document.location = allLabelsLink.href;
                }
            }
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

/**
 * Nastavení typu tisku štítku
 */
export const set_print_setting = (
    nonce: string,
    orderId: number,
    shipmentId: number,
    initialValue: string,
    optionals: any
): void => {
    disableButtons(orderId);

    // @ts-ignore
    const PPLczPlugin = window.PPLczPlugin = window.PPLczPlugin || [];
    const containerEl = jQuery("<div>").prependTo("body")[0];

    let unmount: any = null;
    let render: any = null;
    let currentValue = initialValue;
    let cachedResponse: string | null = null;

    const onFinish = (): void => {
        unmount();

        if (cachedResponse) {
            updateOverlay(orderId, cachedResponse);
        } else {
            ajaxPost('pplcz_change_print', {
                print: currentValue,
                orderId,
                shipmentId,
                nonce
            }).done((response: any) => {
                updateOverlay(orderId, response.html);
            });
        }
    };

    const onChange = (newValue: string): void => {
        currentValue = newValue;
        cachedResponse = null;

        render({ optionals, value: currentValue, onFinish, onChange });

        ajaxPost('pplcz_change_print', {
            print: currentValue,
            orderId,
            shipmentId,
            nonce
        }).done((response: any) => {
            cachedResponse = response.html;
        });
    };

    PPLczPlugin.push(["selectLabelPrint", containerEl, {
        optionals,
        value: currentValue,
        onFinish,
        onChange,
        returnFunc: (data: any) => {
            unmount = data.unmount;
            render = data.render;
        }
    }]);
};

/**
 * Přidání štítků do dávky
 */
export const create_labels_add = (nonce: string, orderId: number, shipment: any): void => {
    // @ts-ignore
    const PPLczPlugin = window.PPLczPlugin || [];
    const containerEl = document.createElement("div");
    document.body.append(containerEl);

    let unmount: any = null;

    PPLczPlugin.push(["selectBatch", containerEl, {
        items: {
            items: [{ orderId, shipmentId: shipment.id }]
        },
        onClose: () => {
            ajaxPost('pplcz_order_panel', { orderId, nonce })
                .done((response: any) => {
                    unmount();
                    updateOverlay(orderId, response.html);
                });
        },
        returnFunc: (data: any) => {
            unmount = data.unmount;
        },
    }]);
};

/**
 * Vytvoření nových štítků s dialogem
 */
export const create_labels2 = (nonce: string, orderId: number, shipment: any): void => {
    disableButtons(orderId);

    // @ts-ignore
    const PPLczPlugin = window.PPLczPlugin = window.PPLczPlugin || [];
    const containerEl = jQuery("<div>").prependTo("body")[0];
    let unmount: any = null;

    PPLczPlugin.push(["newLabel", containerEl, {
        hideOrderAnchor: false,
        shipment,
        returnFunc: (data: any) => {
            unmount = data.unmount;
        },
        onFinish: () => {
            ajaxPost('pplcz_order_panel', { orderId, nonce })
                .done((response: any) => {
                    unmount();
                    updateOverlay(orderId, response.html);
                });
        }
    }]);
};

/**
 * Vytvoření štítků pro zásilku
 */
export const create_labels = (nonce: string, orderId: number, shipmentId?: number): void => {
    disableButtons(orderId);

    ajaxPost('pplcz_order_panel_create_labels', { orderId, shipmentId, nonce })
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