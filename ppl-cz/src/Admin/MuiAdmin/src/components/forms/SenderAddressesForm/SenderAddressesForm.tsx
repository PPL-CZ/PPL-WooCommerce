import { FormProvider, useFieldArray, useForm } from "react-hook-form";
import SenderAddressForm from "./SenderAddressForm";
import Button from "@mui/material/Button";
import Grid from "@mui/material/Grid";
import IconButton from "@mui/material/IconButton";
import AddIcon from "@mui/icons-material/Add";
import { components } from "../../../schema";
import React, { useMemo, useState } from "react";
import { useSenderAddressesMutation } from "../../../queries/settings";
import SelectInput from "../Inputs/SelectInput";
import { ValidationErrorException } from "../../../queries/types";
import SavingProgress from "../../SavingProgress";
type SenderAddressModel = components["schemas"]["SenderAddressModel"];

const SenderAddressesForm = (props: { data: SenderAddressModel[] }) => {
  const data = useMemo(() => {
    if (props.data.length) return props.data;
    return [
      {
        id: -new Date().getTime(),
      },
    ] as SenderAddressModel[];
  }, []);

  const { mutateAsync, error } = useSenderAddressesMutation();

  const [update, setUpdate] = useState(false);

  const formContext = useForm<{ data: SenderAddressModel[] }>({
    values: {
      data,
    },
  });

  const { control, handleSubmit, getValues, setError, formState } = formContext;

  const formData = getValues("data");

  const [position, setPosition] = useState(() => {
    return formData[0].id!;
  });

  const { append, remove, fields } = useFieldArray({
    control,
    name: "data",
  });

  const optionals = fields.map((x, index) => {
    return {
      label: `Adresa pro etiketu (${index + 1})`,
      id: `${formData[index].id}`,
    };
  });

  const current = (() => {
    const pos = formData.filter((x: SenderAddressModel) => x.id === position);
    return { label: pos[0].addressName, id: pos[0].id };
  })();

  const Selector = (
    <SelectInput
      optionals={optionals}
      value={`${current.id}`}
      onChange={id => {
        setPosition(parseInt(id!));
      }}
      // Doplnit kontrolu chyb o výpis konkrétní chyby
      error={!!formState.errors?.data?.length ? "Adresa/y obsahují chyby" : undefined}
      disableClearable
      endAdornment={
        fields.length >= 3 ? null : (
          <IconButton>
            <AddIcon
              onClick={e => {
                const id = -new Date().getTime();
                append({
                  id: -new Date().getTime(),
                } as SenderAddressModel);
                setPosition(id);
              }}
            />
          </IconButton>
        )
      }
    />
  );

  return (
    <form
      onSubmit={handleSubmit(async data => {
        try {
          setUpdate(true);
          await mutateAsync(data.data);
        } catch (errors) {
          if (errors instanceof ValidationErrorException) {
            const data = errors.data;
            Object.keys(data.errors).forEach(x => {
              // @ts-ignore
              setError(`data.${x}`, { type: "validation", message: data.errors[x][0] });
            });
          }
        } finally {
          setUpdate(false);
        }
      })}
    >
      {update ? <SavingProgress /> : false}
      <FormProvider {...formContext}>
        <Grid container alignItems={"center"} justifyContent={"flex-end"}>
          <Grid item xs={4} />
          <Grid item xs={8}>
            {Selector}
          </Grid>

          {fields.map((field, index) => {
            const id = getValues(`data.${index}.id`)!;
            if (id === position) {
              return <SenderAddressForm key={field.id} index={index} />;
            }
            return null;
          }).filter(x => x)[0] || null}

          <Grid item xs={4}/>
          <Grid item xs={8}>
            <Button type="submit">Uložit adresy</Button>
          </Grid>
        </Grid>
      </FormProvider>
    </form>
  );
};

export default SenderAddressesForm;
