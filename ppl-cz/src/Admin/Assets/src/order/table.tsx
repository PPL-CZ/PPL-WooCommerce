const InitOrderTable = (form) => {
    setTimeout(() => {
        jQuery("#cb-select-all-1, #cb-select-all-1").off("click.pplcz_table_column").on("click.pplcz_table_column", function () {
            setTimeout(function () {
                if (!jQuery("input").toArray().some(function (item) {
                    const jQueryItem = jQuery(item);
                    if (jQueryItem.is(":checked") && jQueryItem.data("pplcz-shipment-data-create-shipment")) {
                        return true;
                    }
                    return false;
                })) {
                    jQuery("#pplcz-create-shipments").hide();
                } else {
                    jQuery("#pplcz-create-shipments").show();
                }
            });
        })


        function show_create_labels(ev) {
            ev.preventDefault();
            const orderIds = [];
            const output = [];
            jQuery("input:checked").toArray().some(function (item) {
                // @ts-ignore
                const val = item.value;
                const jQueryItem = jQuery(`#pplcz-order-${val}-overlay`);
                const shipments = jQueryItem.data('shipments');
                if (shipments) {
                    orderIds.push(val);
                    shipments.forEach(function (shipment) {
                        output.push({
                            shipment
                        });
                    })
                }
            })

            if (output.length) {
                var unmount = null;
                // @ts-ignore
                const PPLczPlugin = window.PPLczPlugin || [];
                const div = jQuery("<div>").appendTo("body");
                PPLczPlugin.push([
                    "newLabels",
                    div[0],
                    {
                        shipments: output,
                        "returnFunc": function (data) {
                            unmount = data.unmount;
                        },
                        onFinish: function () {
                            // @ts-ignore
                            wp.ajax.post({
                                action: "pplcz_orders_table",
                                orderIds: orderIds
                            }).done(function (item) {
                                Object.keys(item.orders).forEach(function (key) {
                                    jQuery("#pplcz-order-" + key + "-overlay").replaceWith(item.orders[key]);
                                })
                            });
                            unmount();
                        },
                        onRefresh: function (orderIds) {
                            // @ts-ignore
                            wp.ajax.post({
                                action: "pplcz_orders_table",
                                orderIds: orderIds
                            }).done(function (item) {
                                Object.keys(item.orders).forEach(function (key) {
                                    jQuery("#pplcz-order-" + key + "-overlay").replaceWith(item.orders[key]);
                                })
                            });
                        }
                    }
                ]);
            }
        }

        jQuery("#doaction2, #doaction").off("click.pplcz-create-shipments").on("click.pplcz-create-shipments", function (ev) {
            var value = jQuery("#bulk-action-selector-top").val();
            if (value === 'pplcz_bulk_operation_create_labels') {
                show_create_labels(ev);
            }
        });

        jQuery("#wc-orders-filter #pplcz-create-shipments").off("click.pplcz-create-shipments").on("click.pplcz-create-shipments", show_create_labels);

        jQuery(".pplcz-order-table-panel").each(function (item) {

            const data = jQuery(this).data('shipments');
            const id = jQuery(this).data('orderid');
            if (data) {
                jQuery(`#cb-select-${id}`).off("click.pplcz-create-shipment").on("click.pplcz-create-shipment", function (e) {
                    const checkbox = jQuery(this);
                    if (checkbox.is(":checked")) {
                        jQuery("#pplcz-create-shipments").show();
                    } else if (jQuery("input[type=checkbox]:checked").toArray().some(function (item) {
                        if (item.id.indexOf("cb-select-")) {
                            // @ts-ignore
                            const val = item.value;
                            if (jQuery(`#pplcz-order-${val}-overlay`).data('orderid')) {
                                return true;
                            }
                        }
                        return false;
                    })) {
                        jQuery("#pplcz-create-shipments").show();
                    } else {
                        jQuery("#pplcz-create-shipments").hide();
                    }
                });
            }
        });
    }, 1000);
}

export default InitOrderTable;