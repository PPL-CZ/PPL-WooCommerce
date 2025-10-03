import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { components } from "../../../schema";
import { FormProvider, useForm } from "react-hook-form";

import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import FormLabel from "@mui/material/FormLabel";
import Grid from "@mui/material/Grid";

import TextFieldController from "../Controllers/TextFieldController";
import { baseConnectionUrl } from "../../../connection";
import {useEffect, useState} from "react";
import { UnknownErrorException, ValidationErrorException } from "../../../queries/types";
import CountryController from "../Controllers/CountryController";
import SavingProgress from "../../SavingProgress";
import SaveInfo from "./SaveInfo";

type RecipientAddressModel = components["schemas"]["RecipientAddressModel"];

const RecipienAddressForm = (props: {
  address?: RecipientAddressModel;
  shipmentId: number;
  onChange: (id: number) => void;
  onFinish?: () => void;
}) => {
  const formContext = useForm<RecipientAddressModel>({
    defaultValues: props.address,
  });
  const { control, handleSubmit, setError } = formContext;

  const [disabled, setDisabled] = useState(false);
  const [successTimeout, setSuccessTimeout] = useState(0);

  useEffect(() => {
    formContext.reset(props.address, {
      keepSubmitCount: false,
      keepDirty: false,
      keepErrors: false,
      keepValues: false,
    })
  }, [props.address]);

  const { mutateAsync } = useMutation({
    mutationFn: async (address: RecipientAddressModel) => {
      const defs = baseConnectionUrl();
      await fetch(`${defs.url}/ppl-cz/v1/shipment/${props.shipmentId}/recipient`, {
        method: "PUT",
        headers: {
          "X-WP-nonce": defs.nonce,
          "content-type": "application/json",
        },
        body: JSON.stringify(address),
      })
        .then(async x => {
          if (x.status === 400) {
            const data = await x.json();
            throw new ValidationErrorException(x.status, data.data);
          } else if (x.status > 400) throw new UnknownErrorException(x.status);
          return x;
        })
        .then(x => {
          props.onChange(props.shipmentId);
          setDisabled(false);
          setSuccessTimeout(5000);
        });
    },
  });

  return (
    <FormProvider {...formContext}>
      {" "}
      <form
        onSubmit={handleSubmit(async fields => {
          setDisabled(true);
          try {
            await mutateAsync(fields);
          } catch (errors) {
            if (errors instanceof ValidationErrorException) {
              const data = errors.data;
              Object.keys(data.errors).forEach(x => {
                // @ts-ignore
                setError(`${x}`, { type: "validation", message: data.errors[x][0] });
              });
            }
          } finally {
            setDisabled(false);
          }
        })}
      >
        {disabled ? <SavingProgress /> : null}
        {disabled ? null : <SaveInfo timeout={successTimeout} color={"info"}>Adresa byla uložena</SaveInfo>}
        <Box className="modalBox">
          <Grid container alignContent={"top"} p={2}>
            <Grid item>
              <Box p={1}>
                <FormLabel>Jméno</FormLabel>
                <TextFieldController name="name" control={control} />
              </Box>
              <Box p={1}>
                <FormLabel>Kontaktní osoba</FormLabel>
                <TextFieldController name="contact" control={control} />
              </Box>

              <Box p={1}>
                <FormLabel>Ulice a č.p.</FormLabel>
                <TextFieldController name="street" control={control} />
              </Box>
              <Box p={1}>
                <FormLabel>Obec</FormLabel>
                <TextFieldController name="city" control={control} />
              </Box>
              <Box p={1}>
                <FormLabel>PSČ</FormLabel>
                <TextFieldController name="zip" control={control} />
              </Box>
              <Box p={1}>
                <FormLabel>Země</FormLabel>
                <CountryController name="country" control={control} />
              </Box>
            </Grid>
            <Grid item>
              <Box p={1}>
                <FormLabel>Mail</FormLabel>
                <TextFieldController name="mail" control={control} />
              </Box>
              <Box p={1}>
                <FormLabel>Telefon</FormLabel>
                <TextFieldController name="phone" control={control} />
              </Box>
            </Grid>
          </Grid>
        </Box>
        <hr />
        <Box p={2}>
          <Grid container alignItems={"center"}>
            <Grid item xs={6}>
              <Button disabled={disabled} type="submit">
                Upravit
              </Button>
            </Grid>
            <Grid item xs={6} textAlign="right">
              <Button
                onClick={e => {
                  setDisabled(true);
                  e.preventDefault();
                  props.onFinish?.();
                }}
                type="submit"
              >
                Zavřít
              </Button>
            </Grid>
          </Grid>
        </Box>
      </form>
    </FormProvider>
  );
};

export default RecipienAddressForm;
