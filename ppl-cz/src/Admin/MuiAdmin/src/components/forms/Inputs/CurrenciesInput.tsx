import { useQueryCurrencies } from "../../../queries/codelists";
import SelectInput from "../Inputs/SelectInput";

const CurrenciesInput = (props: {
  value: string;
  onChange: (e: string) => void;
  error?: string;
  version?: "small";
}) => {
  const currencies = useQueryCurrencies();

  return (
    <SelectInput
      key={currencies?.length}
      onChange={e => {
        props.onChange(e || "");
      }}
      value={props.value}
      optionals={(currencies ?? []).map(x => {
        return {
          label: props.version === "small" ? x.code : x.title,
          id: x.code,
        };
      })}
      error={props.error}
      disableClearable
    />
  );
};

export default CurrenciesInput;
