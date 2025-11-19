import metadata from './block.json';
import { ValidatedTextInput, registerCheckoutBlock, extensionCartUpdate, getRegisteredBlock, ExperimentalOrderShippingPackages  } from '@woocommerce/blocks-checkout';
import { useSelect } from "@wordpress/data";
import { __ } from '@wordpress/i18n';

import {Fragment, useEffect, useRef, useState, useMemo, Component} from "react";
const { registerPlugin } = window.wp.plugins;


const getShippingRate = (cart) =>
{
	return cart?.shipping_rates?.find(x => x.rate_id.indexOf("pplcz_") > -1 && x.selected);
}

const metaData = (cart) => {
	return getShippingRate(cart)?.meta_data;
}

const getMetaValue = (cart, key) => {
	return metaData(cart)?.find(x => x.key === key)?.value;
}

const isMapAllowed = (cart) => !!parseInt(getMetaValue(cart, "mapEnabled"));

const isParcelRequired = (cart) => !!parseInt(getMetaValue(cart, "parcelRequired"));

const getHiddenPoints = (cart) => {

	const allowParcels = {
		ParcelBox: "parcelBoxEnabled" ,
		ParcelShop: "parcelShopEnabled" ,
		AlzaBox: "alzaBoxEnabled"
	}

	const meta = metaData(cart) || [];

	return Object.entries(allowParcels).filter(([key, metaKey]) => {
		return !meta.some((x) => x.key === metaKey && x.value);
	}).map(([key]) => key);

}



const getAllowedCountries = (cart) => getMetaValue(cart, "enabledParcelCountries") ?? [];


const parcelShopSelected = (cart) => {
	return cart?.extensions?.["pplcz_parcelshop"]?.["parcel-shop"];
}

const ParcelShop = (props) => {

	const { cart, parcelRequired } = props;
	const parcelShop = parcelShopSelected(cart);
	return (
		<div>
			{parcelShop ?
				<>
					<strong>{__("Výdejní místo", "ppl-cz")}</strong><br/>
					<span>{parcelShop.name}</span>,&nbsp;
					<span>{parcelShop.street}</span>,&nbsp;
					<span>{parcelShop.zipCode}</span>,&nbsp;
					<span>{parcelShop.city}</span>
					<a href={"#"} onClick={e=>{e.preventDefault();PplMap(()=>{}, { lat: parcelShop?.gps.latitude, lng: parcelShop?.gps.longitude});}}>[{__("na mapě", "ppl-cz")}]</a>
				</> : (parcelRequired ? <>{__("Vyberte výdejní místo pro doručení zásilky", "ppl-cz")}</> : <>{__("Zboží bude dodáno na doručovací adresu", "ppl-cz")}</>)}
		</div>
	)
}

const restyle = (idsValues, cart, parcelShopBoxSelected)=> {

	const text = idsValues.map(x => {
		const finded = cart?.shipping_rates?.find(y => y.rate_id === x);

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

		const className =  cart.shipping_rates.length !== 1 ? `.wc-block-components-shipping-rates-control  input[value=${x}]+div:before`: `.wc-block-components-shipping-rates-control  .wc-block-components-radio-control__label:before`;
		if (cart.shipping_rates.length === 1)
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

	//const { checkoutExtensionData } = props;

	const { cart : storeCard, payment } = useSelect((select)=> ({
		cart: select("wc/store/cart").getCartData(), // kde najdu konkrétní metody?
		payment: select("wc/store/payment").getActivePaymentMethod()
	}));

	const idsValues = [...new Set(storeCard?.shippingRates?.reduce((acc, x) => {
		return acc.concat(x.shipping_rates?.map(y => y.rate_id) || []);
	}, []).sort() || [])].sort();

	const firstShippingMethod = storeCard?.shippingRates?.[0];
	const rateId = firstShippingMethod.shipping_rates.find(x => x.selected === true && x.method_id.indexOf("pplcz_") > -1);
	const shippingAddress = firstShippingMethod.destination;
	const parcelShopBoxSelected = parcelShopSelected(storeCard);
	const onUpdateComponent = useRef(false);

	const { parcelRequired, mapAllowed, hiddenPoints, allowedCountries, mapSetting} = useMemo(() => {

			const parcelRequired = isParcelRequired(firstShippingMethod);
			const mapAllowed = isMapAllowed(firstShippingMethod);

			const hiddenPoints = getHiddenPoints(firstShippingMethod);
			const allowedCountries = getAllowedCountries(firstShippingMethod);

			let address = '';
			let country = '';

			if(shippingAddress){
				address = [
					[shippingAddress.address_1, shippingAddress.address_2].filter(x=>x).join(' '),
					[shippingAddress.postcode, shippingAddress.city].filter(x=>x).join(' ')
				].filter(x => x).join(', ');
				country = shippingAddress.country?.toLowerCase() || '';
			}
			if (parcelShopBoxSelected) {
				address = [
					parcelShopBoxSelected.street,
					[parcelShopBoxSelected.zipCode, parcelShopBoxSelected.city].filter(x => x).join(' ')
				].filter(x => x).join(', ');
				country = parcelShopBoxSelected.country || '';
			}
			const mapSetting = {
				address,
				country,
				hiddenPoints: hiddenPoints.length ? hiddenPoints.join(',') : null,
				countries: allowedCountries.map(c => c.toLowerCase()).join(',')
			};



			return { parcelRequired, mapAllowed, hiddenPoints, allowedCountries, mapSetting };
	}, [rateId]);

	window.pplczLastPplMapData = mapSetting;



	const isCart =Array.from(document.getElementsByTagName("meta")).some(x => {
		return x.property === 'pplcz:cart' && x.content === "1";
	});

	const hideComponent = !parcelRequired || !mapAllowed || isCart;

	useEffect(() => {

			if (!onUpdateComponent.current) {
				onUpdateComponent.current = true;
				return;
			}
			if (parcelRequired && !parcelShopBoxSelected && !hideComponent) {
				PplMap(savingData, {...mapSetting});
			}

	}, [parcelRequired, parcelShopBoxSelected]);

	useEffect( () => {
			if (firstShippingMethod)
				restyle(idsValues, firstShippingMethod, parcelShopBoxSelected);

	}, [...idsValues, parcelShopBoxSelected?.accessPointType])

	if (hideComponent)
		return null;

	const savingData = (parcelShop) => {

                extensionCartUpdate({
                    namespace: 'pplcz_parcelshop',
                    data: {
                        "parcel-shop": parcelShop
                    }
                });

            }



	let messages = [];

console.log(parcelRequired, mapSetting, savingData)
	if (parcelRequired && !parcelShopBoxSelected)
		messages.push(<li key={"ageControl"}>{__("Pro dodání zboží je nutno vybrat jedno z výdejních míst", "ppl-cz")}</li>);

	return (
		<>
			<div>
				<ParcelShop  cart={storeCard} parcelRequired={parcelRequired}/> <a href="#withCard" className={"pplcz-select-parcelshop"} onClick={e => {
				e.preventDefault();
				PplMap(savingData, {...mapSetting } );
			}}>{__("Výběr výdejního místa", "ppl-cz")}</a> {parcelShopBoxSelected ? <> / <a href={"#"} className={"pplcz-clear-map"} onClick={e=>{
				e.preventDefault();
				onUpdateComponent.current = false;
				savingData(null);

			}}>{__("Zrušit výběr", "ppl-cz")}</a></> : null} <br/>
				{messages ? <ul>{messages}</ul>:null}
			</div>
		</>)
}

const options = {
	metadata,
	component: Block
};


registerCheckoutBlock(options);