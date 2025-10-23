import { components } from "../../schema";
import LabelShipmentForm from "../forms/LabelShipmentsForm";

type ShipmentWithAdditionalModel = components["schemas"]["ShipmentWithAdditionalModel"];

const BatchShipmentGrid = (props: {
  batchId: string;
  isLoading: boolean;
  data: ShipmentWithAdditionalModel[] | undefined;
}) => {
  const { data: availableBatchs } = props;

  return <>{availableBatchs ? <LabelShipmentForm batchId={props.batchId} models={availableBatchs} /> : null}</>;
};

export default BatchShipmentGrid;
