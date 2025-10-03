import metadata from './block.json';
import { ValidatedTextInput, registerCheckoutBlock, extensionCartUpdate, getRegisteredBlock, ExperimentalOrderShippingPackages  } from '@woocommerce/blocks-checkout';
import { useSelect } from "@wordpress/data";
import { useState, Fragment, useEffect} from "react";
const { registerPlugin } = window.wp.plugins;



const Block = (props) => {

	const { checkoutExtensionData } = props;


	const { cart, payment } = useSelect((select)=> ({
		cart: select("wc/store/cart").getCartData(),
		payment: select("wc/store/payment").getActivePaymentMethod()
	}));

	const [value, setValue] = useState("");
	const [active, setActive] = useState(false);
	const [loading, setLoading] = useState(false);
	const [exten, setExten] = useState(false);
	const [result, setResult] = useState([]);

	useEffect(() => {
		if (exten)
			setExten(false);
	},[cart?.shippingAddress?.address_1 ?? '', cart?.shippingAddress?.city ?? '', cart?.shippingAddress?.postcode ?? '']);


	useEffect(() => {
		const stop = setTimeout(() => {
			if (value) {
				setLoading(true);
				const strs = value.split(",").map((x, key) => {
					switch(key) {
						case 0:
							return `street=${encodeURIComponent(x)}`;
						case 1:
							return `city=${encodeURIComponent(x)}`;
						case 2:
							return `zipCode=${encodeURIComponent(x)}`;
					}
					return null;
				}).filter(x => !!x).join('&');

				fetch(cart.extensions.pplcz_whisperer.url + "?" + strs).then(x => x.json()).then(x=> {
					setResult(x);
				}).finally(()=>setLoading(false));
			}
		}, 700);
		return () => clearTimeout(stop);
	}, [value]);


	if (!cart?.extensions.pplcz_whisperer?.active)
	{
		return null;
	}

	return (
		<><div className={`wc-block-components-text-input ${active || value ? "is-active": ''}`}>
			<input value={value} onChange={e=>{
				setValue(e.target.value);
				setResult(c => c.length > 0 ? [] : c);
			}} onFocus={e=>{
				setActive(true);
			}} onBlur={e=>{
				setActive(false);
			}} type={"text"} id={"whisperer"} aria-label={"Vyhledání ulice"} />
			{loading || exten || true  ?
				<svg style={{
					position: "relative",
					
				}} xmlns='http://www.w3.org/2000/svg' width={"2em"} height={"2em"} viewBox='0 0 200 200'>
					<circle fill='none' strokeOpacity='1' stroke='#FF156D' strokeWidth='.5' cx='100' cy='100' r='0'>
						<animate attributeName='r' calcMode='spline' dur='2' values='1;80' keyTimes='0;1'
								 keySplines='0 .2 .5 1' repeatCount='indefinite'></animate>
						<animate attributeName='stroke-width' calcMode='spline' dur='2' values='0;25' keyTimes='0;1'
								 keySplines='0 .2 .5 1' repeatCount='indefinite'></animate>
						<animate attributeName='stroke-opacity' calcMode='spline' dur='2' values='1;0' keyTimes='0;1'
								 keySplines='0 .2 .5 1' repeatCount='indefinite'></animate>
					</circle>
				</svg> : null}
			<label for={"whisperer"}>Vyhledání ulice</label>
		</div>
		{result.length ? <>
			Kliknutím na konkretní ulici se zapíše do odesílací adresy, kterou pak zkontrolujte a doplňte, nebo
			výběr <a href={"#"} onClick={e => {
			e.preventDefault();
			setResult([]);
		}}>zavřete.</a>
			<ul>
				{result.map((x, k) => {
					return <li key={k}>
						<a href='#' onClick={e => {
							setResult([]);
							extensionCartUpdate({
								namespace: 'pplcz_whisperer',
								data: {
									"address": x
								}
							});
							setExten(true);
						}}>{x.street}, {x.zipCode}, {x.city}</a>
					</li>
				})}
			</ul>
		</>: null}
		</>
	);
}

const options = {
	metadata,
	component: Block
};


registerCheckoutBlock(options);

