import { Fragment, useMemo } from "react";
import Box from "@mui/material/Box";
import Card from "@mui/material/Card";
import Checkbox from "@mui/material/Checkbox";
import FormControlLabel from "@mui/material/FormControlLabel";
import FormLabel from "@mui/material/FormLabel";
import Grid from "@mui/material/Grid";
import TextField from "@mui/material/TextField";
import Typography from "@mui/material/Typography";

import { Controller, useForm, useFormContext } from "react-hook-form";
import { components } from "../../schema";
import { useQueryShipmentStates } from "../../queries/settings";
import { Skeleton } from "@mui/material";
import { useEffect, useState } from "react";
import { useUpdateShipmentPhasesMutation } from "../../queries/useShipmentQueries";
import { useOrderStatuses } from "../../queries/codelists";
import SelectInput from "./Inputs/SelectInput";

type UpdateSyncPhasesModel = components["schemas"]["UpdateSyncPhasesModel"];

const Check = (props: { name: string; label: string; checked: boolean; orderState: string | null }) => {
  const [checked, setChecked] = useState(() => props.checked);
  const [orderState, setOrdeState] = useState(() => props.orderState);

  const { mutateAsync } = useUpdateShipmentPhasesMutation();
  const { data: statusesData } = useOrderStatuses();

  const statuses = useMemo(() => {
    if (!statusesData) return null;

    return statusesData.map(x => ({
      id: x.code,
      label: x.title,
    }));
  }, [statusesData]);

  return (
    <tr>
      <td style={{ width: "240px" }}>
        <FormControlLabel
          control={
            <Checkbox
              name={props.name}
              checked={checked}
              onChange={e => {
                setChecked(!checked);
                mutateAsync({
                  phases: [
                    {
                      code: props.name,
                      watch: !checked,
                      orderState: orderState,
                    },
                  ],
                });
              }}
            />
          }
          label={props.label}
        />
      </td>
      <td>
        {statuses ? (
          <SelectInput
            optionals={statuses}
            value={orderState || undefined}
            onChange={newOrderState => {
              setOrdeState(newOrderState || null);
              mutateAsync({
                phases: [
                  {
                    code: props.name,
                    watch: checked,
                    orderState: newOrderState,
                  },
                ],
              });
            }}
          />
        ) : null}
      </td>
    </tr>
  );
};

const ShipmentPhaseForm = () => {
  const { control, resetField, getValues } = useForm<UpdateSyncPhasesModel>();
  const { data, isLoading } = useQueryShipmentStates();
  const { mutateAsync } = useUpdateShipmentPhasesMutation();

  useEffect(() => {
    if (data)
      setTimeout(() => {
        resetField("maxSync", { defaultValue: data.maxSync });
      });
  }, [data]);

  return (
    <Card id="sync">
      <Box paddingTop={2} paddingBottom={2} paddingLeft={2} paddingRight={2}>
        <Typography variant="h3" marginBottom={4}>
          Synchronizace objednávek
        </Typography>
        <Typography marginBottom={4}>
          <strong>Limity</strong>
        </Typography>
        <Grid container alignItems={"center"}>
          <Grid item xs={4} display={"flex"} alignContent={"center"}>
            <FormLabel>Synchronizovat max</FormLabel>
          </Grid>
          <Grid item xs={8}>
            <Controller
              name="maxSync"
              control={control}
              render={({ field: { onChange, value }, formState }) => (
                <TextField
                  value={value}
                  size="medium"
                  name={"maxSync"}
                  onChange={onChange}
                  onBlur={e => {
                    const maxSync = getValues("maxSync");
                    if (maxSync) {
                      mutateAsync({ maxSync });
                    }
                  }}
                  InputProps={{
                    type: "number",
                  }}
                  helperText={"Maximální počet synchronizovaných objednávek během jednoho požadavku"}
                />
              )}
            />
          </Grid>
        </Grid>
        <Typography marginTop={4} marginBottom={2}>
          <strong>Synchronizovat dle stavu</strong>
        </Typography>
        <Typography marginBottom={4}>
          Pokud má objednávka zásilku s jedním z vybraných stavů, bude sledovaná.
        </Typography>
        {isLoading || !data ? (
          <Skeleton height={150} sx={{ transform: "scale(1,1)" }} />
        ) : (
          <table
            style={{
              width: "100%",
            }}
          >
            <tbody>
              {(data.phases || []).map(x => {
                return <Check key={x.code} checked={x.watch} label={x.title} name={x.code} orderState={x.orderState} />;
              })}
            </tbody>
          </table>
        )}
      </Box>
    </Card>
  );
};

export default ShipmentPhaseForm;
