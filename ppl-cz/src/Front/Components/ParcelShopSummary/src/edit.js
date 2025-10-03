import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import "./edit.style.css";


export const Edit = ({ attributes, setAttributes }) => {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<div className={"wc-block-components-totals-wrapper"}>
			<div className={'wc-block-components-totals-shipping'}>
				<div className={'wc-block-components-totals-item'}>
					Informace o dopravě v rámci parcelshopu
				</div>
			</div>
		</div>
		</div>

)
};

