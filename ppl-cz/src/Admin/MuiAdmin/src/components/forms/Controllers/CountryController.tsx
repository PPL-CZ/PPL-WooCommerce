import type { FieldValues, FieldPath, ControllerProps } from "react-hook-form";
import { Controller } from "react-hook-form";

import CountriesInput from "../Inputs/CountriesInput";

const CountryController = <
  TFieldValues extends FieldValues = FieldValues,
  TName extends FieldPath<TFieldValues> = FieldPath<TFieldValues>
>(
  props: Omit<ControllerProps<TFieldValues, TName>, "render"> & { type?: string }
) => {
  return (
    <Controller
      name={props.name}
      control={props.control}
      render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
        return <CountriesInput value={value} onChange={onChange} error={error?.message || ""} />;
      }}
    />
  );
};

export default CountryController;
