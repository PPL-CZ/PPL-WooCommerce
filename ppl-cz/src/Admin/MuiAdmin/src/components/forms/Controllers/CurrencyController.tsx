import type { FieldValues, FieldPath, ControllerProps } from "react-hook-form";
import { Controller } from "react-hook-form";
import CurrenciesInput from "../Inputs/CurrenciesInput";

const CurrencyController = <
  TFieldValues extends FieldValues = FieldValues,
  TName extends FieldPath<TFieldValues> = FieldPath<TFieldValues>
>(
  props: Omit<ControllerProps<TFieldValues, TName>, "render"> & { type?: string; version?: "small" }
) => {
  return (
    <Controller
      name={props.name}
      control={props.control}
      render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
        return (
          <CurrenciesInput version={props.version} value={value} onChange={onChange} error={error?.message || ""} />
        );
      }}
    />
  );
};

export default CurrencyController;
