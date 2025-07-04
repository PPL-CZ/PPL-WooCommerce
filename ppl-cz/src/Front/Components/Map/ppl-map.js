function PplMap (onComplete, data) {

    const { withCard, withCash, lat, lng, parcelShop, parcelBox, address, country, countries, hiddenPoints } = data || {};

    var pplMap = document.createElement("div");
    pplMap.id = "pplcz-parcelshop-map-overlay"


    var url = new URL(PPLCZ_MAP_JS.mapurl);


    if (parseFloat(lat + "") && parseFloat(lng + "")) {
        url.searchParams.set("ppl_lat", lat);
        url.searchParams.set("ppl_lng", lng);
    }

    if (address)
    {
        url.searchParams.set("ppl_address", address)
    }

    if (withCard) {
        url.searchParams.set("ppl_withCard", "1");
    }
    if (withCash)
    {
        url.searchParams.set("ppl_withCash", "1");
    }

    if (country) {
        url.searchParams.set('ppl_country', country);
    }

    if (parcelShop) {
        url.searchParams.set("ppl_parcelshop", "1");
    }
    if (parcelBox) {
        url.searchParams.set("ppl_parcelbox",  "1")
    }
    if (hiddenPoints)
        url.searchParams.set("ppl_hiddenpoints", hiddenPoints);

    if (countries)
        url.searchParams.set("ppl_countries", countries);


    var stringurl = '' + url;

    jQuery(pplMap).html('<div id="pplcz-parcelshop-map-overlay2">' +
        '<a id="pplcz-parcelshop-map-close" href="#" >Zavřít</a>' +
        '<iframe id="pplcz-parcelshop-map" src="' + stringurl + '"></iframe>' +
        '</div>');

    jQuery(document.body).prepend(pplMap);

    function clear() {
        jQuery(pplMap).remove();
        window.removeEventListener("message", postEvent);
        jQuery("body").removeClass("pplcz-parcelshop-hidden-overlay");
        jQuery("#wpadminbar").removeClass("pplcz-parcelshop-hidden-overlay-wpadminbar");
    }

    function postEvent (event) {
        const domain = url.protocol + "//" + url.host;
        if (event.origin && event.origin === domain) {
            try {
                const parsedData = JSON.parse(event.data);
                if (parsedData.parcelshop){
                   
                    onComplete(parsedData.parcelshop);
                    clear();
                }
            }
            catch (e)
            {
                console.log(e);
            }

        }
    }

    window.addEventListener("message", postEvent);

    jQuery("body").addClass("pplcz-parcelshop-hidden-overlay");
    jQuery("#wpadminbar").addClass("pplcz-parcelshop-hidden-overlay-wpadminbar");

    jQuery("#pplcz-parcelshop-map-close").on("click", function(ev) {
        ev.preventDefault();
        clear();
    })
}