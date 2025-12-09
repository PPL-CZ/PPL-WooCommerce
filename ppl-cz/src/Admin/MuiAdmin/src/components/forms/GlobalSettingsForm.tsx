import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import Grid from "@mui/material/Grid";
import Typography from "@mui/material/Typography";
import Skeleton from "@mui/material/Skeleton";

import { components } from "../../schema";
import { Controller, useForm, Control } from "react-hook-form";
import {useEffect, useState} from "react";
import SavingProgress from "../SavingProgress";
import FormControlLabel from "@mui/material/FormControlLabel";
import Checkbox from "@mui/material/Checkbox";

import {
    useGlobalSettingQuery,
    useGlobalSettingMutation
} from "../../queries/settings";

type GlobalSettingModel = components["schemas"]["GlobalSettingModel"];

const Check = (props: { name: string; label: string; control: Control<GlobalSettingModel, any> }) => {
  return (
    <Controller
      control={props.control}
      name={props.name as any}
      render={({ field, fieldState, formState }) => {
        return (
          <FormControlLabel
            control={
              <Checkbox
                name={props.name}
                checked={!!field.value}
                value={true}
                onChange={e => {
                    field.onChange(!field.value);
                }}
              />
            }
            label={props.label}
          />
        );
      }}
    />
  );
};

const GlobalSettingForm = () => {

  const { reset, handleSubmit, control, setError } = useForm<GlobalSettingModel>();
  const [update, setUpdate] = useState(false);
  const [success, setSuccess] = useState(false);
  const { data, isLoading } = useGlobalSettingQuery();

    useEffect(() => {
        if (data) {
            reset(data)
        }
    }, [JSON.stringify(data)]);


  const { mutateAsync } = useGlobalSettingMutation();

  return (

      <form
        onSubmit={handleSubmit(async fields => {
          setUpdate(true);
          setSuccess(false);
          try {
            await mutateAsync(fields);
            setSuccess(true);
          } catch (error) {
            // Error handling pokud bude potřeba
          } finally {
            setUpdate(false);
          }
        })}
      >
        {update ? <SavingProgress /> : false}
        <Box id="parcelplaces" paddingTop={2} paddingBottom={2} paddingLeft={2} paddingRight={2}>
          <Typography variant="h3" marginBottom={4}>
            Obecné nastavení
          </Typography>
          {isLoading ? (
            <Skeleton height={150} sx={{ transform: "scale(1,1)" }} />
          ) : (
            <Box marginTop={4}>
              <Grid container alignItems={"center"}>
                  <Grid item xs={12} display={"flex"} alignContent={"center"}>
                    <Check label={"Použít OrderNumber (místo ID) pro čísla zásilek"} name={"useOrderNumberInPackages"} control={control} />
                  </Grid>
                  <Grid item xs={12} display={"flex"} alignContent={"center"}>
                      <Check label={"Použít OrderNumber (místo ID) pro vytvoření variabilního symbolu"} name={"useOrderNumberInVariableSymbol"} control={control} />
                  </Grid>
              </Grid>
            </Box>
          )}
          <Box marginTop={4} marginBottom={4}>
            <Button type="submit">Uložit</Button>
          </Box>
        </Box>
      </form>
  );
};

export default GlobalSettingForm;
