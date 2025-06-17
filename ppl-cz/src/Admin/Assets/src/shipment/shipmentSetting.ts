
export const InitSettingShipment = (id) => {

    // @ts-ignore
    const PPLczPlugin = window.PPLczPlugin = window.PPLczPlugin || [];
    const setting = jQuery(id).data("pplczshipmentsetting")

    const code = setting.code;

    let rerender = null;

    const retValues = PPLczPlugin.push(["shipmentSetting", id, {
        setting,
        returnFunc: (retValues) => {
            rerender = retValues.rerender;
        }
    }]);

    jQuery(`[name=woocommerce_${code}_cost_by_weight]`).on("change", function(ev) {
        if (rerender) {
            rerender({
                setting,
                costByWeight: jQuery(this).is(":checked")
            })
        }
    })

}

export default InitSettingShipment;