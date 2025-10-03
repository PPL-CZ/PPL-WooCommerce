import { Controller, useFormContext } from "react-hook-form";
import { components } from "../../../schema";
import CircularProgress from "@mui/material/CircularProgress";
import MenuItem from "@mui/material/MenuItem";
import Select from "@mui/material/Select";
import { useQueryShipmentMethods } from "../../../queries/codelists";

type UpdateShipmentModel = components["schemas"]["UpdateShipmentModel"];
type ShipmentMethodModel = components["schemas"]["ShipmentMethodModel"];

const ServiceSelect = () => {
  const data = useQueryShipmentMethods();
  const { control } = useFormContext<UpdateShipmentModel>();

  if (!data) {
    return <CircularProgress />;
  }

  return (
    <Controller
      name={"serviceCode"}
      control={control}
      render={({ field: { onChange, value }, formState }) => (
        <Select value={value}>
          {data.map(x => {
            return (
              <MenuItem key={x.code} value={x.code}>
                {x.title}
              </MenuItem>
            );
          })}
        </Select>
      )}
    />
  );
};

export default ServiceSelect;
