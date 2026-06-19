(function() {

    document.addEventListener(
        "ppl-parcelshop-map",
        (event) => {

            window.top.postMessage(JSON.stringify({
                "parcelshop": event.detail
            }))

        }
    );

    const widget = document.querySelector("ppl-access-point-widget");
    widget.addEventListener("ppl-accesspointwidget-select", (e) => {

        let type = e.detail.type || e.detail.accessPointType;

        switch (type)
        {
            case 'ALZA_BOX':
                type = 'AlzaBox';
                break;
            case 'PPL_SHOP':
                type = 'ParcelShop';
                break;
            default:
                type = 'ParcelBox';
        }

        const detail = {
            'name' : e.detail.name,
            'code' : e.detail.code,
            'accessPointType' : type,
            'gps': {
                'latitude': e.detail.address.gps.lat,
                'longitude': e.detail.address.gps.lon,
            },
            'city': e.detail.address.city,
            'country': e.detail.address.countryCode,
            'street': e.detail.address.street,
            'zipCode': e.detail.address.zipCode,
            'id': e.detail.externalId,
            'activeCardPayment': e.detail.paymentMethods.card,
            'activeCashPayment': e.detail.paymentMethods.cash
        }

        window.top.postMessage(JSON.stringify({
            "parcelshop": detail
        }))
    });
})();