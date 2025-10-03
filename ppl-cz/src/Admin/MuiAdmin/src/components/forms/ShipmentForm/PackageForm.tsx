import { useFormContext } from "react-hook-form";
import { components } from "../../../schema";
import TextFieldController from "../Controllers/TextFieldController";
import AddIcon from "@mui/icons-material/Add";
import RemoveIcon from "@mui/icons-material/Remove";
import IconButton from "@mui/material/IconButton";
import TableCell from "@mui/material/TableCell";
import TableRow from "@mui/material/TableRow";

type UpdateShipmentModel = components["schemas"]["UpdateShipmentModel"];

const PackageForm = (props: {
  index: number;
  max: number;
  add: (index: number) => void;
  remove: (index: number) => void;
}) => {
  const { control, watch } = useFormContext<UpdateShipmentModel>();
  const serviceCode = watch("serviceCode");

  return (
    <TableRow>
      <TableCell>
        <TextFieldController emptyValue={""} control={control} name={`packages.${props.index}.referenceId`} />{" "}
      </TableCell>
      <TableCell>
        <TextFieldController
          emptyValue={() => undefined}
          control={control}
          name={`packages.${props.index}.insurance`}
          type="number"
        />
      </TableCell>
      <TableCell>
        {["PRIV", "PRID", "SMAR", "SMAD"].indexOf(serviceCode || "") > -1 ? (
          <IconButton
            size="small"
            onClick={e => {
              props.add(props.index + 1);
            }}
          >
            <AddIcon />
          </IconButton>
        ) : null}
        {props.index !== 0 ? (
          <IconButton
            size="small"
            onClick={e => {
              props.remove(props.index);
            }}
          >
            <RemoveIcon />
          </IconButton>
        ) : null}
      </TableCell>
    </TableRow>
  );
};

export default PackageForm;
