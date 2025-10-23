import SelectBatchForm from "../forms/SelectBatchForm";
import { components } from "../../schema";
type PrepareShipmentBatchModel = components["schemas"]["PrepareShipmentBatchModel"];

interface SelectBatchWidgetProps {
  onClose?: () => void;
  items: PrepareShipmentBatchModel;
}

const SelectBatchWidget = ({ items, onClose }: SelectBatchWidgetProps) => {
  return <SelectBatchForm items={items} onContinue={onClose} />;
};

export default SelectBatchWidget;
