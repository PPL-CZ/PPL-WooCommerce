import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import "./edit.style.css";


export const Edit = ({ attributes, setAttributes }) => {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<div className={ 'whisperer-shop-block-fields' }>
				<p>
					Vyhledávání adresy
				</p>
			</div>
		</div>
	);
};
