import { useFormContext } from "react-hook-form";
import FormLabel from "@mui/material/FormLabel";
import Grid from "@mui/material/Grid";
import TextFieldController from "../Controllers/TextFieldController";

import { components } from "../../../schema";
import CountryController from "../Controllers/CountryController";
type SenderAddressModel = components["schemas"]["SenderAddressModel"];

const SenderAddressForm = (props: { index: number }) => {
  const index = props.index;
  const { control } = useFormContext<{ data: SenderAddressModel[] }>();

  return (
    <>
      <Grid item xs={4}>
        <FormLabel>Název adresy</FormLabel>
      </Grid>
      <Grid item xs={8}>
        <TextFieldController name={`data.${index}.addressName`} control={control} />
      </Grid>
      <Grid item xs={4}>
        <FormLabel>Jméno (firma)</FormLabel>
      </Grid>
      <Grid item xs={8}>
        <TextFieldController name={`data.${index}.name`} control={control} />
      </Grid>

      <Grid item xs={4}>
        <FormLabel>Kontaktní osoba</FormLabel>
      </Grid>
      <Grid item xs={8}>
        <TextFieldController name={`data.${index}.contact`} control={control} />
      </Grid>
      <Grid item xs={4}>
        <FormLabel>Ulice a č.p.</FormLabel>
      </Grid>
      <Grid item xs={8}>
        <TextFieldController name={`data.${index}.street`} control={control} />
      </Grid>
      <Grid item xs={4}>
        <FormLabel>Město</FormLabel>
      </Grid>
      <Grid item xs={8}>
        <TextFieldController name={`data.${index}.city`} control={control} />
      </Grid>
      <Grid item xs={4}>
        <FormLabel>PSČ</FormLabel>
      </Grid>
      <Grid item xs={8}>
        <TextFieldController name={`data.${index}.zip`} control={control} />
      </Grid>
      <Grid item xs={4}>
        <FormLabel>Země</FormLabel>
      </Grid>
      <Grid item xs={8}>
        <CountryController control={control} name={`data.${index}.country`} />
      </Grid>
      <Grid item xs={4}>
        <FormLabel>Mail</FormLabel>
      </Grid>
      <Grid item xs={8}>
        <TextFieldController name={`data.${index}.mail`} control={control} />
      </Grid>
      <Grid item xs={4}>
        <FormLabel>Telefon</FormLabel>
      </Grid>
      <Grid item xs={8}>
        <TextFieldController name={`data.${index}.phone`} control={control} />
      </Grid>
      <Grid item xs={4}>
        <FormLabel>Poznámka</FormLabel>
      </Grid>
      <Grid item xs={8}>
        <TextFieldController name={`data.${index}.note`} control={control} />
      </Grid>
    </>
  );
};

export default SenderAddressForm;
