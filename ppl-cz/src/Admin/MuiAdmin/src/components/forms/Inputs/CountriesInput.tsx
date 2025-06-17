import { useQueryCountries } from "../../../queries/codelists";
import SelectInput from "../Inputs/SelectInput";

const CountriesInput = (props: { value: string; onChange: (e: string) => void; error?: string }) => {
  const currencies = useQueryCountries();

  return (
    <SelectInput
      key={currencies?.length}
      onChange={e => {
        props.onChange(e || "");
      }}
      value={props.value}
      optionals={(currencies ?? [])?.map(x => {
        return {
          label: x.title,
          id: x.code,
        };
      })}
      disableClearable
      error={props.error}
    />
  );
};

export default CountriesInput;
