import { dispatch } from "@wordpress/data";

let observer = false;
const elements:any[] = [];

export function parcelshop(element) {

    if (!observer)
    {

        observer = true;
        let timeout = null;
        const mut = new MutationObserver((mutations) => {

            for (let mutation of mutations) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach((node) => {
                        if (timeout)
                            return;

                        timeout = setTimeout(()=> {
                            timeout = false;
                            jQuery(".pplcz_parcelshop_orderitems").each(function() {
                                if (elements.indexOf(this) > -1)
                                    return;
                                elements.push(this);
                                parcelshop(this);
                            })
                        }, 500);
                    });
                }
            }
        });
        const config = {
            childList: true, subtree: true
        }
        mut.observe(jQuery('#post-body')[0], config);
    }

    const input = jQuery(element).find(`input`)
    const meta_id = input.data('meta_id');
    const order_id =  input.data('order_id');
    const nonce = input.data('nonce');

    let container = jQuery(element);

    const error = () => {
        // @ts-ignore
        dispatch("core/notices").createNotice(
            'error', // Can be one of: success, info, warning, error.
            'Problém se změnou parcelshop/parcelboxu.', // Text string to display.
            {
                isDismissible: true, // Whether the user can dismiss the notice.
            }
        );
    }

    const clickName = `pplcz_parcelshop_${meta_id}`;

    jQuery(`.${clickName}`).off(`.${clickName}`);

    const onComplete = (shipping_address:any) => {

        jQuery.ajax({
            // @ts-ignore
            url: pplcz_data.ajax_url,
            type: "post",
            dataType: "json",
            data: {
                action: "pplcz_render_parcel_shop",
                meta_id: meta_id,
                order_id,
                shipping_address,
                nonce
            },
            error,
            success: (data) => {
                if (data.success) {
                    const newcontent = jQuery(data.data.content);
                    container.replaceWith(newcontent);
                    newcontent.show();
                    parcelshop(newcontent);
                    newcontent.find('button').css('display', 'inline');
                }
                else
                {
                    // @ts-ignore
                    error();
                }

            }
        });
    }

    container.addClass(clickName).on(`click.${clickName}`, ".pplcz_parcelshop_parcelshop",function(e) {
        e.preventDefault();
        PplMap(onComplete, { parcelShop: true })
    }).on(`click.click.${clickName}`, ".pplcz_parcelshop_parcelbox",function(e) {
        e.preventDefault();
        PplMap(onComplete, { parcelBox: true })
    }).on(`click.click.${clickName}`, ".pplcz_parcelshop_clear",function(e) {
        e.preventDefault();
        onComplete(null);
    });


    const parent = container.closest(`tr[data-order_item_id="${meta_id}"]`);
    parent.addClass(clickName).one(`click.${clickName}`, "a.edit-order-item",()=>{
        container.find("button").css("display", "inline");
        setTimeout(() => {
            parent.find(`select`).filter((x, y)=> {
                return y.name === `shipping_method[${meta_id}]`;
            }).addClass(clickName).on(`change.${clickName}`, function() {
                const val = jQuery(this).val();
                if (val.includes("pplcz_")) {
                    container.show();
                    container.find('button').css('display', 'block');
                } else  {
                    container.hide();
                    container.find('button').css('display', 'none');
                }
            })
        }, 300);
    });
}

export default parcelshop;