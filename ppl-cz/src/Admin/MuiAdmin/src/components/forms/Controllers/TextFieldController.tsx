import type { FieldValues, FieldPath, ControllerProps } from "react-hook-form";
import { Controller, useFormContext } from "react-hook-form";
import TextField from "@mui/material/TextField";
import { useState } from "react";

const TextFieldController = <
  TFieldValues extends FieldValues = FieldValues,
  TName extends FieldPath<TFieldValues> = FieldPath<TFieldValues>
>(
  props: Omit<ControllerProps<TFieldValues, TName>, "render"> & {
    type?: string;
    emptyValue?: any;
    size?: "medium" | "small";
  }
) => {

  const { unregister } = useFormContext();
  const [undf, setUndf] = useState(0);

  return (
    <Controller
      key={undf}
      name={props.name}
      control={props.control}
      render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
        return (
          <TextField
            value={value}
            inputProps={{
              type: props.type ?? "text",
            }}
            size={props.size}
            name={props.name}
            onChange={e => {
              if (e.target.value === "") {

                if (props.emptyValue === null) onChange(null);
                else if (typeof props.emptyValue === "function") {
                  const val = props.emptyValue(e.target.value);
                  if (val === undefined) {
                    unregister(props.name);
                    setUndf(c => c + 1);
                  } else {
                    onChange(val);
                  }
                } else if (props.emptyValue) {
                  onChange(props.emptyValue);
                } else {
                  onChange(e);
                }
              } else {
                onChange(e);
              }
            }}
            error={!!error}
            helperText={error?.message}
          />
        );
      }}
    />
  );
};
export default TextFieldController;
