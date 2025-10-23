import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import Card from "@mui/material/Card";
import Grid from "@mui/material/Grid";
import Typography from "@mui/material/Typography";
import Skeleton from "@mui/material/Skeleton";

import { components } from "../../schema";
import { Controller, useForm, Control } from "react-hook-form";
import { useEffect, useMemo, useState } from "react";
import SavingProgress from "../SavingProgress";
import FormControlLabel from "@mui/material/FormControlLabel";
import Checkbox from "@mui/material/Checkbox";
import { useQueryCountries } from "../../queries/codelists";
import { useParcelPlacesQuery, useParcelPlacesMutation } from "../../queries/settings";

type ParcelPlacesModel = components["schemas"]["ParcelPlacesModel"];

const Check = (props: { name: string; label: string; control: Control<ParcelPlacesModel, any> }) => {
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

const LanguageCheck = (props: {
  label: string;
  language: string;
  otherLanguage: string;
  control: Control<ParcelPlacesModel, any>;
}) => {
  return (
    <Controller
      control={props.control}
      name={"mapLanguage"}
      render={({ field, fieldState, formState }) => {
        const checked = field.value === props.language;
        return (
          <FormControlLabel
            control={
              <Checkbox
                id={props.language}
                name={`language_${props.language}`}
                checked={checked}
                onChange={e => {
                  if (field.value === props.language) field.onChange(props.otherLanguage);
                  else field.onChange(props.language);
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

const CountryCheck = (props: { label: string; country: string; control: Control<ParcelPlacesModel, any> }) => {
  return (
    <Controller
      control={props.control}
      name={"disabledCountries"}
      render={({ field, fieldState, formState }) => {
        const checked = (field.value ?? []).indexOf(props.country) > -1;
        return (
          <FormControlLabel
            control={
              <Checkbox
                id={`checkBox-${props.country}`}
                checked={checked}
                name={`disabledCountries_${props.country}`}
                onChange={e => {
                  if (checked) field.onChange((field.value || []).filter(x => x !== props.country));
                  else field.onChange((field.value || []).concat([props.country]));
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

const ParcelPlaces = () => {
  const { reset, handleSubmit, control, setError } = useForm<ParcelPlacesModel>();
  const [update, setUpdate] = useState(false);
  const [success, setSuccess] = useState(false);
  const { data, isLoading } = useParcelPlacesQuery();
  const qCountries = useQueryCountries();
  const { mutateAsync } = useParcelPlacesMutation();

  const countries = useMemo(() => {
    return qCountries?.filter(x => x.parcelAllowed).map(x => ({ id: x.code, label: x.title })) ?? [];
  }, [qCountries]);

  useEffect(() => {
    if (data) {
      setTimeout(() => {
        reset(data);
      });
    }
  }, [data]);

  return (
    <Card>
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
            Výdejní místa, která nelze používat
          </Typography>
          {isLoading ? (
            <Skeleton height={150} sx={{ transform: "scale(1,1)" }} />
          ) : (
            <Box marginTop={4}>
              <Grid container alignItems={"center"}>
                <Grid item xs={2} display={"flex"} alignContent={"center"}>
                  <Check label={"ParcelBox"} name={"disabledParcelBox"} control={control} />
                </Grid>
                <Grid item xs={2} />
                <Grid item xs={2} display={"flex"} alignContent={"center"}>
                  <Check label={"ParcelShop"} name={"disabledParcelShop"} control={control} />
                </Grid>
                <Grid item xs={2} />
                <Grid item xs={2} display={"flex"} alignContent={"center"}>
                  <Check label={"AlzaBox"} name={"disabledAlzaBox"} control={control} />
                </Grid>
              </Grid>
            </Box>
          )}
          {countries.length ? (
            <>
              <Typography variant="h3" marginTop={4} marginBottom={4}>
                Země, které nelze použít v rámci výdejních míst
              </Typography>
              <Box marginTop={4} marginBottom={4}>
                {countries.map(x => (
                  <CountryCheck key={x.id} label={x.label} country={x.id} control={control} />
                ))}
              </Box>
            </>
          ) : (
            false
          )}
          <Typography variant="h3" marginTop={4} marginBottom={4}>
            Jazyk mapy
          </Typography>
          <Box marginTop={4} marginBottom={4}>
            <Grid container alignItems={"center"}>
              <Grid item xs={2} display={"flex"} alignContent={"center"}>
                <LanguageCheck label={"Čeština"} language={"CS"} otherLanguage={"EN"} control={control} />
              </Grid>
              <Grid item xs={2} display={"flex"} alignContent={"center"}>
                <LanguageCheck label={"Angličtina"} language={"EN"} otherLanguage={"CS"} control={control} />
              </Grid>
            </Grid>
            <Button type="submit">Uložit</Button>
          </Box>
        </Box>
      </form>
    </Card>
  );
};

export default ParcelPlaces;
