import { Fragment } from "react";
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

type UpdateSyncPhasesModel = components["schemas"]["UpdateSyncPhasesModel"];

const Check = (props: { name: string; label: string; checked: boolean }) => {
  const [checked, setChecked] = useState(() => props.checked);
  const { mutateAsync } = useUpdateShipmentPhasesMutation();

  return (
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
                },
              ],
            });
          }}
        />
      }
      label={props.label}
    />
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
                  name={'maxSync'}
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
          <>
            {(data.phases || []).map(x => {
              return (
                <Fragment key={x.code} >
                  <Check checked={x.watch} label={x.title} name={x.code} />
                  <br />
                </Fragment>
              );
            })}
          </>
        )}
      </Box>
    </Card>
  );
};

export default ShipmentPhaseForm;
