

document.addEventListener(
    "ppl-parcelshop-map",
    (event) => {

        window.top.postMessage(JSON.stringify({
            "parcelshop": event.detail
        }))
    }
);

