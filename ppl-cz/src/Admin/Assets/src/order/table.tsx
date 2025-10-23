/**
 * Inicializace funkcionalit pro tabulku objednávek
 */
const InitOrderTable = (form: any): void => {
    setTimeout(() => {
        setupSelectAllCheckbox();
        setupBulkActions();
        setupCreateShipmentsButton();
        setupIndividualCheckboxes();
    }, 1000);
};

/**
 * Kontrola, zda jsou vybrány nějaké zásilky
 */
const hasSelectedShipments = (): boolean => {
    return jQuery("input:checked").toArray().some((checkbox) => {
        const $checkbox = jQuery(checkbox);
        return $checkbox.data("pplcz-shipment-data-create-shipment");
    });
};

/**
 * Nastavení checkboxu "Select All"
 */
const setupSelectAllCheckbox = (): void => {
    jQuery("#cb-select-all-1, #cb-select-all-2")
        .off("click.pplcz_table_column")
        .on("click.pplcz_table_column", () => {
            setTimeout(() => {
                jQuery("#pplcz-create-shipments").toggle(hasSelectedShipments());
            });
        });
};

/**
 * Získání všech vybraných zásilek z checkboxů
 */
const getSelectedShipments = (): Array<{ shipmentId?: number; orderId?: number }> => {
    const items: Array<{ shipmentId?: number; orderId?: number }> = [];



    jQuery("input:checked").each((i, el) => {
        if (el.tagName === "INPUT")
        {
        const orderId = (() => {
            const val = `${jQuery(el).val()}`;
            return val;
        })();
        const shipments = jQuery(`#pplcz-order-${orderId}-overlay`).data('shipments');

        if (shipments) {
            shipments.forEach((shipment: any) => {
                if (shipment.id) {
                    items.push({ shipmentId: shipment.id });
                } else {
                    items.push({ orderId: shipment.orderId });
                }
            });
        }
        }
    });

    return items;
};

/**
 * Zobrazení dialogu pro výběr dávky
 */
const showBatchSelectionDialog = (ev: Event): void => {
    ev.preventDefault();

    const selectedItems = getSelectedShipments();

    if (selectedItems.length === 0) return;

    // @ts-ignore
    const PPLczPlugin = window.PPLczPlugin || [];
    const containerEl = document.createElement("div");
    document.body.append(containerEl);

    let unmount: any = null;

    PPLczPlugin.push(["selectBatch", containerEl, {
        items: { items: selectedItems },
        onClose: () => unmount?.(),
        returnFunc: (data: any) => {
            unmount = data.unmount;
        },
    }]);
};

/**
 * Nastavení bulk akcí
 */
const setupBulkActions = (): void => {
    jQuery("#doaction2, #doaction")
        .off("click.pplcz-create-shipments")
        .on("click.pplcz-create-shipments", (ev) => {
            const selectedAction = jQuery("#bulk-action-selector-top").val();
            if (selectedAction === 'pplcz_bulk_operation_create_labels') {
                showBatchSelectionDialog(ev);
            }
        });
};

/**
 * Nastavení tlačítka "Vytvořit zásilky"
 */
const setupCreateShipmentsButton = (): void => {
    jQuery("#wc-orders-filter #pplcz-create-shipments")
        .off("click.pplcz-create-shipments")
        .on("click.pplcz-create-shipments", showBatchSelectionDialog);
};

/**
 * Nastavení individuálních checkboxů pro zásilky
 */
const setupIndividualCheckboxes = (): void => {
    jQuery(".pplcz-order-table-panel").each(function() {
        const shipments = jQuery(this).data('shipments');
        const orderId = jQuery(this).data('orderid');

        if (!shipments) return;

        jQuery(`#cb-select-${orderId}`)
            .off("click.pplcz-create-shipment")
            .on("click.pplcz-create-shipment", function() {
                const isChecked = jQuery(this).is(":checked");

                if (isChecked || hasSelectedShipments()) {
                    jQuery("#pplcz-create-shipments").show();
                } else {
                    jQuery("#pplcz-create-shipments").hide();
                }
            });
    });
};

export default InitOrderTable;