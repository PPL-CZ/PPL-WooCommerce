
export const shipment = () => {

    // @ts-ignore
    const dph = jQuery(jQuery("input").toArray().filter((x:HTMLInputElement) => x.name.indexOf("_priceWithDph") > -1)[0]);

    // @ts-ignore
    const disablePayments = jQuery(jQuery("select").toArray().filter((x:HTMLInputElement) => x.name.indexOf("_disablePayments") > -1)[0]);

    disablePayments.closest('tr').before(`<tr><td colspan="50"><hr></td></tr>`);
    const inputs = dph.closest('table')
        .find("input")
        .toArray()
        .filter(( x) => x.name.indexOf('pplcz_') > -1)
        .filter(( x) => x.name.match(/_[A-Z]{3}$/));

    const currencies = inputs.map(( x) => x.name.match(/[A-Z]{3}$/)).reduce((acc, currency)=> {
        if (acc.indexOf(currency[0]) >-1)
            return acc;
        acc.push(currency[0]);
        return acc;
    }, [] as string[]);


    const allInputs = inputs.map(x => x.name);

    const titleId = 'pplcz_title';

    const data = jQuery(`<tr>
      <th colspan="50" class="titledesc">
          ${currencies.map(x => {
        return `<a href='#currency_${x}'>${x}</a>`;
    }).join(" | ")}
        <hr>
            <div id='${titleId}'></div>
      </th>
</tr>`).on('click', 'a', function (ev) {
        ev.preventDefault();
        jQuery(ev.currentTarget).closest('tr').find('a').css('font-weight', 'normal');
        jQuery(ev.currentTarget).css('font-weight', 'bold');

        const currency = `${ev.currentTarget.href}`.match(/currency_([A-Z]{3})$/)[1];

        allInputs.forEach(function (x) {
            const cur = x.substring(x.length - 3);
            if (cur === currency)
                jQuery('#' + x).closest('tr').show();
            else
                jQuery('#' + x).closest('tr').hide();
        });

        jQuery(`#${titleId}`).html(`Nastavení pro měnu: ${currency}`)

    });

    dph.closest('tr').after(data);
    dph.closest('table').find('input[type=checkbox]').closest('label').css('min-width', '250px')
    allInputs.forEach(function(x){
        if (x.indexOf("cost_automatic_recalculation_") !== -1)
        {
            jQuery(`#${x}`).on('change', function() {
                const currency = x.substring(x.length - 3);
                const updatedInput = allInputs.filter(item => item.substring(item.length-3) === currency).map(x => jQuery(`#${x}`).closest('tr'));
                if (jQuery(this).is(":checked")) {
                    updatedInput.forEach(x => {
                        x.find('.shipment-price-original').hide();
                        x.find('.shipment-price-base').show();
                    });
                } else {
                    updatedInput.forEach(x => {
                        x.find('.shipment-price-original').show();
                        x.find('.shipment-price-base').hide();
                    });
                }
            }).trigger('change');
        }
    })
    data.find('a:first-child').click();
}

export default shipment;