import metadata from './block.json';
import { registerCheckoutBlock,  extensionCartUpdate, registerCheckoutFilters } from '@woocommerce/blocks-checkout';
import { useSelect } from "@wordpress/data";
import {useEffect} from "@wordpress/element";


import "./fontend.css";


const mapAllowed = (shipment) => {
	const shipping_rate = shipment?.shipping_rates?.find(x => x.rate_id.indexOf("pplcz_") > -1 && x.selected);
	const mapEnabled = shipping_rate?.meta_data?.find(x => x.key === "mapEnabled");

	if (!parseInt(mapEnabled?.value))
		return false;

	return shipping_rate;

}

const isParcelShopRequired = (shipment) => {


	const shipping_rate = shipment?.shipping_rates.find(x => x.rate_id.indexOf("pplcz_") > -1 && x.selected);
	const parcelRequired = shipping_rate?.meta_data?.find(x => x.key === "parcelRequired");

	if (!parcelRequired)
		return false;
	if (!parseInt(parcelRequired.value))
		return false;

	return true;
}

const isParcelShopSelected = (cart) => {
	const parcelShop = cart.extensions?.["pplcz_parcelshop"]?.["parcel-shop"];
	return !!parcelShop;
}

const ParcelShop = (props) => {
	const { cart } = props;
	const parcelShop = cart.extensions?.["pplcz_parcelshop"]?.["parcel-shop"];
	return  (
		<small>
			<strong>Výdejní místo</strong><br/>
			<span>{parcelShop.name}</span> <a href={"#"} onClick={e => {
			e.preventDefault();
			PplMap(()=>{}, { lat:  parcelShop?.gps.latitude, lng: parcelShop?.gps.longitude});
		}}>[na mapě]</a><br/>
			<span>{parcelShop.street}</span><br/>
			<span>{parcelShop.zipCode} {parcelShop.city}</span><br/>
		</small>
	)
}

const Block = (props) => {

	const { checkoutExtensionData } = props;

	const { cart, payment } = useSelect((select)=> ({
		cart: select("wc/store/cart").getCartData(),
		payment: select("wc/store/payment").getActivePaymentMethod()
	}));

	const shipment = cart?.shippingRates?.[0];

	const shipping_rate = (() => {
		if (shipment)
			return mapAllowed(shipment);
		return undefined;
	})();

	const parcelShopRequired = isParcelShopRequired(shipment)

	useEffect(() => {
		if (!shipment)
			return;

		const className = "wc-block-components-shipping-address-hide-send-address";
		if (shipping_rate && parcelShopRequired && isParcelShopSelected(cart))
		{
			document.body.className += " " + className;
		} else {
			document.body.className = document.body.className.split(/\s+/g).filter(x => x !== className).join(" ");
		}

	}, [shipping_rate?.rate_id, parcelShopRequired]);


	if (!shipment || !shipping_rate || !parcelShopRequired ||  !isParcelShopSelected(cart))
		return null;

	return <div className={'wp-block-woocommerce-checkout-order-summary-shipping-block wc-block-components-totals-wrapper'}>
		<div className={"wc-block-components-totals-item"}>
			<ParcelShop cart={cart}/>
		</div>
	</div>
}

const options = {
	metadata,
	component: Block
};

registerCheckoutBlock(options);


wp.hooks.addAction('experimental__woocommerce_blocks-checkout-render-checkout-form', 'parcel-shop-summary-block', () => {
	const payment_method = wp.data.select('wc/store/payment').getActivePaymentMethod();
	window.wc.blocksCheckout.extensionCartUpdate({
		namespace: 'pplcz_refresh_payment',
		data: {
			payment_method: payment_method,
		},
	});
});

wp.hooks.addAction('experimental__woocommerce_blocks-checkout-set-active-payment-method', 'parcel-shop-summary-block', (payment_method) => {
	window.wc.blocksCheckout.extensionCartUpdate({
		namespace: 'pplcz_refresh_payment',
		data: {
			payment_method: payment_method.value || payment_method.paymentMethodSlug
		},
	});
});