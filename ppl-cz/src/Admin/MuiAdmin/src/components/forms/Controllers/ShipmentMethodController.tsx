import type { FieldValues, FieldPath, ControllerProps } from "react-hook-form";
import { Controller } from "react-hook-form";
import ShipmentMethodInput from "../Inputs/ShipmentMethodsInput";

const ShipmentMethodController = <
  TFieldValues extends FieldValues = FieldValues,
  TName extends FieldPath<TFieldValues> = FieldPath<TFieldValues>
>(
  props: Omit<ControllerProps<TFieldValues, TName>, "render"> & { type?: string }
) => {
  return (
    <Controller
      name={props.name}
      control={props.control}
      render={({ field: { onChange, value }, formState, fieldState }) => {
          let message = '';
          if (typeof formState.errors?.[props.name]?.message === "string")
              message = formState.errors?.[props.name]?.message as string;
        return <ShipmentMethodInput value={value} onChange={onChange} errors={message} />;
      }}
    />
  );
};

export default ShipmentMethodController;
