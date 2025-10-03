import { components } from "../../schema";
import LabelShipmentForm from "../forms/LabelShipmentsForm";
import ModalO from "../Modal";
type ShipmentModel = components["schemas"]["ShipmentModel"];

const CreateShipmentLabelWidget = (props: {
  shipments: { shipment: ShipmentModel; errors: any }[];
  hideOrderAnchor?: boolean,
  onFinish?: () => void;
  onRefresh?: (orderIds: number[]) => void;
}) => {
  return (
    <ModalO>
      <LabelShipmentForm
        hideOrderAnchor={props.hideOrderAnchor}
        models={props.shipments}
        onFinish={() => props.onFinish?.()}
        onRefresh={ids => props.onRefresh?.(ids)}
      />
    </ModalO>
  );
};

export default CreateShipmentLabelWidget;
