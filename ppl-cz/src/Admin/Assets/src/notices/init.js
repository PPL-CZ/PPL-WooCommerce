export const  InitNotices = () => {
    const element = jQuery('.pplcz-news-notice');
    element.on("click", ".notice-dismiss", function() {
        const nonce = jQuery(this).closest('.pplcz-new-notice').data('nonce');
        wp.ajax.post({ action: "pplcz_hide_new_notice", nonce });
    })
}

export default InitNotices;