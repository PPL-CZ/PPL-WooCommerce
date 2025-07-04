import { useQueryShipmentMethods } from "../../../queries/codelists";
import SelectInput from "./SelectInput";

const ShipmentMethodsInput = (props: { value: string; onChange: (e: string) => void, errors?: string }) => {
  const services = useQueryShipmentMethods();

  const optionals =
    services?.map(x => {
      return {
        label: x.title,
        id: x.code,
      };
    }) ?? [];

  return (
    <SelectInput
      disableClearable
      key={optionals?.length || 0}
      optionals={optionals}
      value={props.value}
      error={props.errors}
      onChange={e => props.onChange(e!)}
    />
  );
};

export default ShipmentMethodsInput;
