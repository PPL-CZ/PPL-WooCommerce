import metadata from './block.json';
import { registerCheckoutBlock,  extensionCartUpdate, registerCheckoutFilters } from '@woocommerce/blocks-checkout';
import { useSelect } from "@wordpress/data";
import {useEffect} from "@wordpress/element";


import "./fontend.css";
const getShippingRate = (shipment) => {
	return shipment?.shipping_rates?.find(x => x.rate_id.indexOf("pplcz_") > -1 && x.selected); }

const getMetaValue = (shipment, key) => {
	return getShippingRate(shipment)?.meta_data?.find(x => x.key === key).value;
}

const getParcelShop = (cart) => cart.extensions?.["pplcz_parcelshop"]?.["parcel-shop"]

const isMapAllowed = (shipment) => !!parseInt(getMetaValue(shipment, "mapEnabled"))

const isParcelShopRequired = (shipment) => !!parseInt(getMetaValue(shipment, "parcelRequired"))

const ParcelShop = ({parcelShop}) => {
	if (!parcelShop) return null;

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

const Block = () => {


	const { cart, payment } = useSelect((select)=> ({
		cart: select("wc/store/cart").getCartData(),
		payment: select("wc/store/payment").getActivePaymentMethod()
	}));

	const shipment = cart?.shippingRates?.[0];

	const shipping_rate = shipment && isMapAllowed(shipment)? getShippingRate(shipment) : undefined;

	const parcelShopRequired = isParcelShopRequired(shipment);

	const parcelShop = getParcelShop(cart);

	useEffect(() => {
		if (!shipment)
			return;

		const className = "wc-block-components-shipping-address-hide-send-address";
		if (shipping_rate && parcelShopRequired && parcelShop)
		{
			document.body.className += " " + className;
		} else {
			document.body.className = document.body.className.split(/\s+/g).filter(x => x !== className).join(" ");
		}

	}, [shipping_rate?.rate_id, parcelShopRequired]);


	if (!shipment || !shipping_rate || !parcelShopRequired || !parcelShop)
		return null;

	return <div className={'wp-block-woocommerce-checkout-order-summary-shipping-block wc-block-components-totals-wrapper'}>
		<div className={"wc-block-components-totals-item"}>
			<ParcelShop parcelShop={parcelShop}/>
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
