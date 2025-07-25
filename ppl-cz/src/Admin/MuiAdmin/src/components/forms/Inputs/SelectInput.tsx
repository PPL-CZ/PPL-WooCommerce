import Autocomplete from "@mui/material/Autocomplete";
import TextField from "@mui/material/TextField";
import React from "react";

type Optional = { id: string; label: string };

type BaseProps = {
    name?: string;
    optionals: Optional[];
    disableClearable?: boolean;

    endAdornment?: React.ReactNode;
    error?: string;
    size?: "medium" | "small";
}

type MultipleFalse = BaseProps &  {
    value?: string;
    multiple?: false;
    onChange: (id?: string) => void;
};

type MultipleTrue = BaseProps & {
    multiple: true
    multipleValue: string[]
    onMultipleChange: (ids: string[]) => void;
}

type Props = MultipleFalse | MultipleTrue;

const SelectInput = (props:Props)=> {
  const { optionals, disableClearable } = props;

  const isSelected = (x: Optional) => {
      if (props.multiple)
          return props.multipleValue.indexOf(x.id) > -1;
      return x.id === props.value
  }

  const current = (!props.multiple) ? (optionals ?? []).filter(isSelected)[0] : ((optionals ?? []).filter(isSelected));

  const key = props.multiple ? props.multipleValue.join(",") : props.value;

  return (
    <Autocomplete
      key={key ?? "no-id-defined"}
      multiple={props.multiple}
      options={props.optionals}
      value={current}
      getOptionLabel={item => {
        return `${item.label}`;
      }}
      disableClearable={disableClearable}
      getOptionKey={item => item.id!}
      onChange={(e, val) => {
          if (!props.multiple)
          {

              const oneId = Array.isArray(val) ? val.pop()?.id: (val|| undefined)?.id
              props.onChange?.(oneId);
          }
          else {

              const multipleId = Array.isArray(val) ? val.map(item => item.id) : [(val || undefined)?.id].filter(i => !!i) as string[];
              props.onMultipleChange?.(multipleId as any)
          }
      }}
      renderOption={(props, options, valueIndex) => {
        return (
          <li {...props} key={options.id || valueIndex.index} >
            <div>
              <span
                style={{
                  fontWeight: isSelected(options) ? 700 : "inherit",
                }}
              >
                {`${options.label}`}
              </span>
            </div>
          </li>
        );
      }}
      renderInput={params => {
        if (props.endAdornment) {
          let endAdporment = params.InputProps.endAdornment;
          // @ts-ignore
          let children = React.Children.toArray(endAdporment.props?.children) as any;
          children = children.concat(props.endAdornment);
          // @ts-ignore
          const endAdornment = React.cloneElement(endAdporment, endAdporment.props, children);
          // @ts-ignore
          return (
            <TextField
              {...params}
              InputProps={{ ...params.InputProps, endAdornment, name: props.name }}
              size={props.size}
              error={!!props.error}
              helperText={props.error}
            />
          );
        }
        return <TextField {...params} size={props.size} error={!!props.error} helperText={props.error} />;
      }}
    />
  );
};

export default SelectInput;
