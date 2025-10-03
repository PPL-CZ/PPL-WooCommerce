import Modal from "../Modal";

import ShipmentForm from "../forms/ShipmentForm/ShipmentForm";
import { components } from "../../schema";

type ShipmentModel = components["schemas"]["ShipmentModel"];

const CreateShipmentWidget = (props: { shipment: ShipmentModel; onFinish?: () => void, onChange?: () => void }) => {
  return (
    <Modal width="max">
      <ShipmentForm shipment={props.shipment} onFinish={() => props.onFinish?.()} onChange={()=> props.onChange?.()} />
    </Modal>
  );
};

export default CreateShipmentWidget;
