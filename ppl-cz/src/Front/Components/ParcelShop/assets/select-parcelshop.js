

let previousPaymentMethod = jQuery('input[name^="payment_method"]:checked').val();

jQuery(document).on("click", "button[data-pplcz-select-parcel-shop],a[data-pplcz-select-parcel-shop]", function(ev) {
   ev.preventDefault();


   const mapSetting = {
       address : jQuery(this).data('address') ?? null,
       country : jQuery(this).data('country') ?? null,
       hiddenPoints : jQuery(this).data('hidden-points') ?? null,
       countries : jQuery(this).data('countries') ?? null
   }


    const what = jQuery(this).data("pplcz-select-parcel-shop");

   const updateCheckout = (data)=> {
       jQuery("[name=pplcz_parcelshop]").val(JSON.stringify(data));
        jQuery("body").trigger("update_checkout");
    }

   switch(what)
   {
       case "cash":
           PplMap(data => updateCheckout(data), { withCash: true, ...mapSetting});
           break;
       case "clear":
           updateCheckout(null);
           break;
       default:

           PplMap(data => updateCheckout(data), mapSetting);
           break;
   }
});


jQuery('body').on('updated_checkout', function() {

    const trigger = jQuery("a[data-pplcz-select-parcel-shop], button[data-pplcz-select-parcel-shop]").first();

    if (trigger.length > 0) {
        window.pplczLastPplMapData = {
            address: trigger.data("address") ?? null,
            country: trigger.data("country") ?? null,
            hiddenPoints: trigger.data("hidden-points") ?? null,
            countries: trigger.data("countries") ?? null
        };
    }
    const showmap = jQuery('.pplcz-parcelshop-inner').data('pplcz-showmap');
    if (showmap == 1) {
        jQuery("a[data-pplcz-select-parcel-shop]").trigger("click")

    }
});


(function ($) {
    $('form.checkout').on('change','input[name^=\"payment_method\"]', function () {
        const selectedPaymentMethod = $('input[name^="payment_method"]:checked').val();
        const payments = [previousPaymentMethod, selectedPaymentMethod];

        //const codMethod = jQuery('a.pplcz-parcel').data('cod-method');
        const codMethod = jQuery('#pplcz_cod_method').val();

        previousPaymentMethod = selectedPaymentMethod;

        if (payments.indexOf(codMethod) > -1)
        {
            $('body').trigger('update_checkout');
        }

    });
})(jQuery);
