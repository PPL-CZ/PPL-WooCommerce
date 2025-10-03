import ProductTab from "./tab";

const InitProductTab = (element) => {
    const el = jQuery(element);
    const pplNonce = el.data('pplnonce');
    const data = el.data("data");
    const methods = el.data('methods');

    ProductTab(el[0], {
        pplNonce,
        data,
        methods
    });
}

export default InitProductTab;
