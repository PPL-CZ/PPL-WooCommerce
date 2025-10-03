import CategoryTab from "./panel";

const InitCategoryTab = (element) => {
    const el = jQuery(element);
    const pplNonce = el.data('pplnonce');
    const data = el.data("data");
    const methods = el.data('methods');

    CategoryTab(el[0], {
        pplNonce,
        data,
        methods
    });
}

export default InitCategoryTab;
