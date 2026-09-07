import { dispatch } from "@wordpress/data";

let isObserverInitialized = false;
const processedElements: any[] = [];

/**
 * Inicializace MutationObserver pro automatické zpracování nově přidaných parcelshop elementů
 */
const initMutationObserver = (): void => {
    if (isObserverInitialized) return;

    isObserverInitialized = true;
    let debounceTimeout: number | null = null;

    const observer = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                if (debounceTimeout) return;

                debounceTimeout = window.setTimeout(() => {
                    debounceTimeout = null;

                    jQuery(".pplcz_parcelshop_orderitems").each(function() {
                        if (processedElements.includes(this)) return;

                        processedElements.push(this);
                        parcelshop(this);
                    });
                }, 500);
            }
        }
    });

    const postBody = jQuery('#post-body')[0];
    if (postBody) {
        observer.observe(postBody, { childList: true, subtree: true });
    }
};

/**
 * Podporuje vybraná dopravní metoda doručení na výdejní místo?
 * Mapa id metody => bool přichází z PHP v pplcz_data.parcelshop_support.
 */
const supportsParcelShop = (methodValue?: string | null): boolean => {
    if (!methodValue || !methodValue.includes("pplcz_")) return false;

    // @ts-ignore
    const support = pplcz_data?.parcelshop_support ?? {};

    return !!support[methodValue.split(":")[0]];
};

export function parcelshop(element: HTMLElement): void {
    initMutationObserver();

    const input = jQuery(element).find('input');
    const metaId = input.data('meta_id');
    const orderId = input.data('order_id');
    const nonce = input.data('nonce');
    const container = jQuery(element);

    const showError = (): void => {
        // @ts-ignore
        dispatch("core/notices").createNotice(
            'error',
            'Problém se změnou parcelshop/parcelboxu.',
            { isDismissible: true }
        );
    };

    const eventNamespace = `pplcz_parcelshop_${metaId}`;

    // Odstranění starých event listenerů
    jQuery(`.${eventNamespace}`).off(`.${eventNamespace}`);

    /**
     * Handler po výběru parcelshop/parcelbox
     */
    const handleParcelSelection = (shippingAddress: any): void => {
        jQuery.ajax({
            // @ts-ignore
            url: pplcz_data.ajax_url,
            type: 'post',
            dataType: 'json',
            data: {
                action: 'pplcz_render_parcel_shop',
                meta_id: metaId,
                order_id: orderId,
                shipping_address: shippingAddress,
                nonce
            },
            error: showError,
            success: (response) => {
                if (response.success) {
                    const newContent = jQuery(response.data.content);
                    container.replaceWith(newContent);
                    newContent.show().find('button').css('display', 'inline');
                    parcelshop(newContent[0]);
                } else {
                    showError();
                }
            }
        });
    };

    // Event listenery pro výběr parcelshop/parcelbox
    container
        .addClass(eventNamespace)
        .on(`click.${eventNamespace}`, '.pplcz_parcelshop_parcelshop', (e) => {
            e.preventDefault();
            // @ts-ignore
            PplMap(handleParcelSelection, { parcelShop: true });
        })
        .on(`click.${eventNamespace}`, '.pplcz_parcelshop_parcelbox', (e) => {
            e.preventDefault();
            // @ts-ignore
            PplMap(handleParcelSelection, { parcelBox: true });
        })
        .on(`click.${eventNamespace}`, '.pplcz_parcelshop_clear', (e) => {
            e.preventDefault();
            handleParcelSelection(null);
        });

    // Zpracování změn shipping metody
    const parentRow = container.closest(`tr[data-order_item_id="${metaId}"]`);

    parentRow
        .addClass(eventNamespace)
        .one(`click.${eventNamespace}`, 'a.edit-order-item', () => {
            container.find('button').css('display', 'inline');

            setTimeout(() => {
                const shippingMethodSelect = parentRow
                    .find('select')
                    .filter((_, element) => element.name === `shipping_method[${metaId}]`);

                shippingMethodSelect
                    .addClass(eventNamespace)
                    .on(`change.${eventNamespace}`, function() {
                        const selectedValue = jQuery(this).val() as string;
                        const isParcelShopMethod = supportsParcelShop(selectedValue);

                        if (!isParcelShopMethod) {
                            // doprava bez výdejního místa – zahodíme dříve vybraný parcelshop/parcelbox
                            container.find(`input[name="pplcz_parcelshop[${metaId}]"]`).val("");
                        }

                        container.toggle(isParcelShopMethod);
                        container.find("button").css("display", isParcelShopMethod ? "block" : "none");
                    });
            }, 300);
        });
}

export default parcelshop;