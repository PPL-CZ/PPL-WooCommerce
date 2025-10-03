import { Controller, useForm, FormProvider } from "react-hook-form";

import {useAddressCollection, useLastCollection, useNewCollection} from "../../queries/useCollectionQueries";
import { components } from "../../schema";
import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import Card from "@mui/material/Card";
import FormLabel from "@mui/material/FormLabel";
import Grid from "@mui/material/Grid";
import IconButton from "@mui/material/IconButton";
import InputAdornment from "@mui/material/InputAdornment";
import Skeleton from "@mui/material/Skeleton";
import TextField from "@mui/material/TextField";
import Typography from "@mui/material/Typography";

import { useEffect, useMemo, useState } from "react";
import { DatePicker, LocalizationProvider } from "@mui/x-date-pickers";
import { AdapterDateFns } from "@mui/x-date-pickers/AdapterDateFnsV3";
import { format, formatDate, parse } from "date-fns";
import Add from "@mui/icons-material/AddCircle";
import Minus from "@mui/icons-material/RemoveCircle";
import TextFieldController from "./Controllers/TextFieldController";
import { ValidationErrorException } from "../../queries/types";
import SavingProgress from "../SavingProgress";

type NewCollectionModel = components["schemas"]["NewCollectionModel"];
type CollectionAddressModel = components["schemas"]["CollectionAddressModel"];

const NewCollectionForm = (props: { onCreate: () => void }) => {
  const { mutateAsync } = useNewCollection();

  const [disabled, setDisabled] = useState(false);
  const sendDate = useMemo(() => {
    const date = new Date();
    date.setDate(date.getDate() + 1);
    return formatDate(date, "yyyy-MM-dd");
  }, []);

  const formCtx = useForm<NewCollectionModel>({
    values: {
      sendDate,
    },
  });

  const { handleSubmit, control, resetField, setError, setValue } = formCtx;

  const { isLoading: isLoading1, isError:isError1, data: collection } = useLastCollection();
  const { isLoading: isLoading2, isError: isError2, data: address} = useAddressCollection();
  const isLoading = isLoading2 || isLoading1;
  const isError = isError1 || isError2;

  useEffect(() => {
    if (collection) {
      setTimeout(() => {
        if (collection.contact) {
          setValue("contact", collection.contact);
        }
        if (collection.email) {
          setValue("email", collection.email );
        }
        if (collection.telephone) {
          setValue("telephone", collection.telephone );
        }
      },200);
    }
  }, [collection]);

  const [update, setUpdate] = useState(false);

  if (isLoading) {
    return <Skeleton height={150} sx={{ transform: "scale(1,1)" }} width={300} />;
  }

  const referenceDate = new Date();

  const readonly = {
    // change label and border color when readonly
    "&:has([readonly]) ": {
      "& .MuiOutlinedInput-root": {
        backgroundColor: '#EAEAEA'
      }
    },
  }


  return (
    <Card>
      <FormProvider {...formCtx}>
        <LocalizationProvider dateAdapter={AdapterDateFns}>
          <form
            onSubmit={handleSubmit((fields: NewCollectionModel) => {
              setDisabled(true);
              setUpdate(true);
              mutateAsync(fields)
                .then(x => {
                  props.onCreate();
                })
                .catch(e => {
                  if (e instanceof ValidationErrorException) {
                    const data = e.data;
                    Object.keys(data.errors).forEach(x => {
                      // @ts-ignore
                      setError(`${x}`, { type: "validation", message: data.errors[x][0] });
                    });
                  }
                })
                .finally(() => {
                  setDisabled(false);
                  setUpdate(false);
                });
            })}
          >
            <Box p={2}>
              {update ? <SavingProgress /> : false}
              <Typography variant="h2" mb={4}>
                Objednávka svozu
              </Typography>

              <Typography variant="h3" mb={4}>
                Podrobnosti svozu
              </Typography>
              <Grid id="coll_detail" container alignItems={"center"}>
                <Grid item xs={4} display={"flex"}>
                  <FormLabel>Datum předání přepravci</FormLabel>
                </Grid>
                <Grid item xs={6} display={"flex"}>
                  <Controller
                    name="sendDate"
                    control={control}
                    render={({ field: { onChange, value }, fieldState: { error }, formState }) => (
                      <DatePicker
                        shouldDisableDate={date => date < referenceDate}
                        value={value ? parse(value, "yyyy-MM-dd", referenceDate) : null}
                        disabled={disabled}
                        format="dd.MM.yyyy"
                        onChange={value => {
                          if (value) onChange(format(value, "yyyy-MM-dd"));
                          else onChange(null);
                        }}
                        slotProps={{
                          textField: {
                            size: "medium",
                            error: !!error,
                            helperText: error?.message,
                          },
                        }}
                      />
                    )}
                  />
                </Grid>
                <Grid item xs={4} display={"flex"}>
                  <FormLabel>Předpokládaný počet balíků</FormLabel>
                </Grid>
                <Grid item xs={6} display={"flex"}>
                  <Controller
                    name="estimatedShipmentCount"
                    control={control}
                    render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
                      const makeInteger = (v: any) => {
                        const m = parseInt(value ? `${value}` : "");
                        if (isNaN(m)) return 0;
                        return m;
                      };

                      const ival = makeInteger(value);

                      return (
                        <TextField
                          disabled={disabled}
                          value={ival}
                          size="medium"
                          onChange={e => {
                            const v = parseInt(e.target.value);
                            if (isNaN(v) || v < 0) onChange(0);
                            onChange(v);
                          }}
                          error={!!error}
                          helperText={error?.message}
                          InputProps={{
                            endAdornment: (
                              <InputAdornment position="end">
                                <IconButton
                                  onClick={e => {
                                    onChange(ival + 1);
                                  }}
                                >
                                  <Add />
                                </IconButton>
                                {ival > 0 ? (
                                  <IconButton
                                    onClick={e => {
                                      onChange(ival - 1);
                                    }}
                                  >
                                    <Minus />
                                  </IconButton>
                                ) : null}
                              </InputAdornment>
                            ),
                          }}
                        />
                      );
                    }}
                  />
                </Grid>
                <Grid item xs={4} display={"flex"}>
                  <FormLabel>Poznámka</FormLabel>
                </Grid>
                <Grid item xs={6} display={"flex"}>
                  <TextFieldController control={control} name={"note"} size="medium" />
                </Grid>
              </Grid>
            </Box>

            <Box p={2}>
              <Typography variant="h3" mb={3}>
                Kontaktní údaje
              </Typography>
              <Grid id="coll_contact" container alignItems={"center"}>
                <Grid item xs={4} display={"flex"}>
                  <FormLabel>Jméno</FormLabel>
                </Grid>
                <Grid item xs={6} display={"flex"}>
                  <TextFieldController control={control} name={"contact"} size="medium" />
                </Grid>
                <Grid item xs={4} display={"flex"}>
                  <FormLabel>Telefon</FormLabel>
                </Grid>
                <Grid item xs={6} display={"flex"}>
                  <TextFieldController control={control} name={"telephone"} size="medium" />
                </Grid>
                <Grid item xs={4} display={"flex"}>
                  <FormLabel>E-mail</FormLabel>
                </Grid>
                <Grid item xs={6} display={"flex"}>
                  <TextFieldController control={control} name={"email"} size="medium" />
                </Grid>
                <Grid item xs={4} /> {/* xsOffset to nezna, i kdyz ho mui5 podporuje */}
              </Grid>
              {address && !isLoading && !isError2 ?
                  <>
                    <Typography variant="h3" mb={3}>
                      Adresa svozu
                    </Typography>
                    <Grid id="coll_address" container alignItems={"center"}>
                      <Grid item xs={4} display={"flex"}>
                        <FormLabel>Jméno</FormLabel>
                      </Grid>
                      <Grid item xs={6} display={"flex"}>
                        <TextField size="medium" sx={readonly} inputProps={{
                          readOnly: true
                        }} value={address.name} />
                      </Grid>
                      <Grid item xs={4} display={"flex"}>
                        <FormLabel>Adresa</FormLabel>
                      </Grid>
                      <Grid item xs={6} display={"flex"}>
                        <TextField size="medium"  sx={readonly} inputProps={{
                          readOnly: true
                        }} value={address.street} />
                      </Grid>
                      <Grid item xs={4} display={"flex"}>
                        <FormLabel>Město</FormLabel>
                      </Grid>
                      <Grid item xs={6} display={"flex"}>
                        <TextField size="medium" sx={readonly} inputProps={{
                          readOnly: true
                        }} value={address.city} />
                      </Grid>
                      <Grid item xs={4} display={"flex"}>
                        <FormLabel>PSČ</FormLabel>
                      </Grid>
                      <Grid item xs={6} display={"flex"}>
                        <TextField size="medium" sx={readonly} inputProps={{
                          readOnly: true
                        }} value={address.zip} />
                      </Grid>
                      <Grid item xs={4} /> {/* xsOffset to nezna, i kdyz ho mui5 podporuje */}
                      <Grid item xs={8}>
                        <Button type="submit">Vytvořit a objednat svoz</Button>
                      </Grid>
                    </Grid>
              </>: null}
            </Box>
          </form>
        </LocalizationProvider>
      </FormProvider>
    </Card>
  );
};

export default NewCollectionForm;
