import metadata from './block.json';
import { ValidatedTextInput, registerCheckoutBlock, extensionCartUpdate, getRegisteredBlock, ExperimentalOrderShippingPackages  } from '@woocommerce/blocks-checkout';
import { useSelect } from "@wordpress/data";
import {Fragment, useEffect, useRef, useState} from "react";
const { registerPlugin } = window.wp.plugins;


const mapAllowed = (shipment) => {

	const shipping_rate = shipment?.shipping_rates?.find(x => x.rate_id.indexOf("pplcz_") > -1 && x.selected);
	const mapEnabled = shipping_rate?.meta_data?.find(x => x.key === "mapEnabled");

	if (!parseInt(mapEnabled?.value))
		return false;

	return true;
}

const isParcelRequired = (shipment) => {

	const shipping_rate = shipment?.shipping_rates?.find(x => x.rate_id.indexOf("pplcz_") > -1 && x.selected);
	const parcelRequired = shipping_rate?.meta_data.find(x => x.key === "parcelRequired");

	if (!parcelRequired)
		return false;
	if (!parseInt(parcelRequired.value))
		return false;

	return true;
}

const getHiddenPoints = (shipment) => {

	const shipping_rate = shipment.shipping_rates?.find(x => x.rate_id.indexOf("pplcz_") > -1 && x.selected);
	const meta_data = shipping_rate?.meta_data;

	const allowedParcels = {
		ParcelBox: true,
		ParcelShop: true,
		AlzaBox: true
	};


	if (!meta_data?.find(x => x.key === "parcelBoxEnabled" && x.value) )
	{
		allowedParcels.ParcelBox = false;
	}

	if (!meta_data?.find(x =>  x.key === "parcelShopEnabled" && x.value) )
	{
		allowedParcels.ParcelShop = false;
	}

	if (!meta_data?.find(x =>  x.key === "alzaBoxEnabled" && x.value) )
	{
		allowedParcels.AlzaBox = false;
	}

	return Object.keys(allowedParcels).filter(x => !allowedParcels[x]);
}

const getAllowedCountries = (shipment) => {

	const shipping_rate = shipment.shipping_rates?.find(x => x.rate_id.indexOf("pplcz_") > -1 && x.selected);
	const meta_data = shipping_rate?.meta_data;

	const allowedCountries = meta_data?.find(x => x.key === "enabledParcelCountries" && x.value)?.value;
	return allowedCountries ?? [];

}

const isParcelShopSelected = (cart) => {
	return cart.extensions?.["pplcz_parcelshop"]?.["parcel-shop"];

}

const ParcelShop = (props) => {
	const { cart, parcelRequired } = props;

	const parcelShop = cart?.extensions?.["pplcz_parcelshop"]?.["parcel-shop"];
	return (
		<div>
			{parcelShop ?
				<>
					<strong>Výdejní místo</strong><br/>
					<span>{parcelShop.name}</span>,&nbsp;
					<span>{parcelShop.street}</span>,&nbsp;
					<span>{parcelShop.zipCode}</span>,&nbsp;
					<span>{parcelShop.city}</span> <a  href={"#"} onClick={e=>{
					e.preventDefault();
					PplMap(()=>{}, { lat: parcelShop?.gps.latitude, lng: parcelShop?.gps.longitude});
				}}>[na mapě]</a></> : (parcelRequired ? <>Vyberte výdejní místo pro doručení zásilky</> : <>Zboží bude dodáno na doručovací adresu</>)}
		</div>
	)
}

const restyle = (idsValues, shippingRates, parcelShopBoxSelected)=> {
	const text = idsValues.map(x => {
		const finded = shippingRates.shipping_rates.find(y => y.rate_id === x);

		if (!finded.rate_id.startsWith("pplcz_")) {
			return "";
		}
		const classNameSet = {
			"background-size": "auto 2em",
			"background-repeat": "no-repeat",
			"display": "inline-block",
			"content": "''",
			"height": "2em"
		}

		let image = `${parcelshop_block_frontend.assets_url}/ppldhl_4084x598.png`;

		if (finded.meta_data.some(x => x.key === "parcelRequired" && x.value == 1 || x.key ==="mapEnabled" && x.value == 1)) {
			image = `${parcelshop_block_frontend.assets_url}/vydejnimista_1329x500.png`
			if (finded.selected) {
				if (parcelShopBoxSelected?.accessPointType === "ParcelShop") {
					image = `${parcelshop_block_frontend.assets_url}/parcelshop_2609x1033.png`
				} else if (parcelShopBoxSelected?.accessPointType === "ParcelBox") {
					image = `${parcelshop_block_frontend.assets_url}/parcelbox_2625x929.png`
				}
			}
		}

		classNameSet["background-image"] = `url('${image}')`;

		const matched = image.match(/_([0-9]+)x([0-9]+)\./)
		const s = 2* matched[1] / matched[2] ;

		classNameSet["width"] = s + "em";

		const className =  shippingRates.shipping_rates.length !== 1 ? `.wc-block-components-shipping-rates-control  input[value=${x}]+div:before`: `.wc-block-components-shipping-rates-control  .wc-block-components-radio-control__label:before`;
		if (shippingRates.shipping_rates.length === 1)
		{
			classNameSet.display = "block";
		}
		const classAll = `${className} {
				${Object.keys(classNameSet).map(x => `${x}: ${classNameSet[x]};`).join("\n")}  
			}`;
		return classAll;
	}).join("\n");

	const style = document.getElementById("ppl-shipping-images") || document.createElement("style");

	if (!style.id) {
		document.head.appendChild(style);
	}

	style.innerHTML = text;
}

const Block = (props) => {

	const { checkoutExtensionData } = props;

	const { cart, payment } = useSelect((select)=> ({
		cart: select("wc/store/cart").getCartData(),
		payment: select("wc/store/payment").getActivePaymentMethod()
	}));

	const idsValues = [...new Set(cart?.shippingRates?.reduce((acc, x) => {
		return acc.concat(x.shipping_rates?.map(y => y.rate_id) || []);
	}, []).sort() || [])].sort();

	const shippingRates = cart?.shippingRates?.[0];


	const parcelRequired = isParcelRequired(shippingRates)

	const parcelShopBoxSelected = isParcelShopSelected(cart);

	const onUpdateComponent = useRef(false);

	let mapSetting = null;

	const isCart =Array.from(document.getElementsByTagName("meta")).some(x => {
		return x.property === 'pplcz:cart' && x.content === "1";
	});

	const hideComponent = !parcelRequired || !mapAllowed(shippingRates) || isCart;

	useEffect(() => {
		if (!onUpdateComponent.current) {
			onUpdateComponent.current = true;
			return;
		}
		if (parcelRequired && !parcelShopBoxSelected && !hideComponent)
		{
			PplMap(savingData, {...mapSetting } );
		}
	}, [parcelRequired, parcelShopBoxSelected]);

	useEffect( () => {
		if (shippingRates)
			restyle(idsValues, shippingRates, parcelShopBoxSelected);
	}, idsValues.concat((parcelShopBoxSelected || {}).accessPointType))


	if (hideComponent)
		return null;

	const  shippingAddress = cart.shippingAddress;
	let mapaddress = '';

	if (shippingAddress) {
		mapaddress  = [
			[shippingAddress.address_1, shippingAddress.address_2].filter(x => x).join(' '),
			[shippingAddress.postcode, shippingAddress.city].filter(x => x).join(' ')
		].filter(x => !!x).join(', ');
	}

	const savingData = (parcelShop) => {
		extensionCartUpdate({
			namespace: 'pplcz_parcelshop',
			data: {
				"parcel-shop": parcelShop
			}
		});
	}

	let messages = [];

	const parcelShop = cart.extensions?.["pplcz_parcelshop"]?.["parcel-shop"];
	mapSetting = {
		address: mapaddress,
	};

	if (shippingAddress?.country) {
		mapSetting.country = shippingAddress.country.toLowerCase();
	}

	if (parcelShop)
	{
		mapSetting.address = [parcelShop.street, [parcelShop.zip, parcelShop.city].join(' ')].join(', ');
		mapSetting.country = parcelShop.country;
	}

	const hiddenPoints = getHiddenPoints(shippingRates);
	const allowedCountries = getAllowedCountries(shippingRates);

	mapSetting.hiddenPoints = hiddenPoints.length ? hiddenPoints.join(",") : null;
	mapSetting.countries = allowedCountries.join(",").toLowerCase();

	if (parcelRequired && !parcelShop)
		messages.push(<li key={"ageControl"}>Pro dodání zboží je nutno vybrat jedno z výdejních míst</li>);

	return (
		<>
			<div>
				<ParcelShop  cart={cart} parcelRequired={parcelRequired}/> <a href="#withCard" onClick={e => {
				e.preventDefault();
				PplMap(savingData, {...mapSetting } );
			}}>Výběr výdejního místa</a> {parcelShop ? <> / <a href={"#"} onClick={e=>{
				e.preventDefault();
				onUpdateComponent.current = false;
				savingData(null);
			}}>Zrušit výběr</a></> : null} <br/>
				{messages ? <ul>{messages}</ul>:null}
			</div>
		</>);
}

const options = {
	metadata,
	component: Block
};


registerCheckoutBlock(options);