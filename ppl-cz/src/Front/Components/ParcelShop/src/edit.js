import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import "./edit.style.css";


export const Edit = ({ attributes, setAttributes }) => {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<div className={ 'parcel-shop-block-fields' }>
				<p>
					Výběr parcelshopu z mapy: <a onClick={e=>{
						e.preventDefault();
					}} >Možnost platby kartou</a> / <a onClick={e => {
						e.preventDefault()
				}} >Možnost platit hotově</a> <br/>
					<small>Viditelné pouze v okamžiku, kdy je vybrána položka jež podporuje parcelshop</small>
				</p>
			</div>
		</div>
	);
};
