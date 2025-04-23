import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import Card from "@mui/material/Card";
import FormLabel from "@mui/material/FormLabel";
import Grid from "@mui/material/Grid";
import TextField from "@mui/material/TextField";
import Typography from "@mui/material/Typography";
import Skeleton from "@mui/material/Skeleton";
import ContentCopyIcon from "@mui/icons-material/ContentCopy";
import CheckIcon from "@mui/icons-material/Check";
import Alert from "@mui/material/Alert";


import { components } from "../../schema";
import { Controller, useForm, Control } from "react-hook-form";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { baseConnectionUrl } from "../../connection";
import { useEffect, useState } from "react";
import SavingProgress from "../SavingProgress";
import FormControlLabel from "@mui/material/FormControlLabel";
import Checkbox from "@mui/material/Checkbox";

type MyApiModel = components["schemas"]["ParcelPlacesModel"];


const Check = (props: { name: string; label: string; control:  Control<MyApiModel, any>  }) => {
  return <Controller control={props.control} name={props.name as any} render={({ field, fieldState, formState, })=>{
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
          )}}
      />;
};



const ParcelPlaces = () => {
  const queryClient = useQueryClient();
  const { reset, handleSubmit, control, setError } = useForm<MyApiModel>();
  const [update, setUpdate] = useState(false);
  const [success, setSuccess] = useState(false);
  const { data, isLoading } = useQuery({
    queryKey: ["parcelplaces"],
    queryFn: async () => {
      const baseUrl = baseConnectionUrl();
      return fetch(`${baseUrl.url}/ppl-cz/v1/setting/parcelplaces`, {
        headers: {
          "X-WP-Nonce": baseUrl.nonce,
        },
      }).then(x => x.json() as Promise<MyApiModel>);
    },
  });

  const { mutateAsync } = useMutation({
    mutationFn: async (data: MyApiModel) => {
      setUpdate(true);
      setSuccess(false);
      const baseUrl = baseConnectionUrl();
      await fetch(`${baseUrl.url}/ppl-cz/v1/setting/parcelplaces`, {
        method: "PUT",
        headers: {
          "X-WP-Nonce": baseUrl.nonce,
          "content-type": "application/json",
        },
        body: JSON.stringify(data),
      })
        .then(async x => {
          if (x.status === 204)
          {
            setSuccess(true);
          }
          return;
        })
        .finally(() => {
          setUpdate(false);
        });
    },
    onSuccess: () => {
      queryClient.refetchQueries({
        queryKey: ["parcelplaces"],
      });
    },
  });

  useEffect(() => {
    if (data) {
      setTimeout(() => {
        reset(data);
      });
    }
  }, [data]);

  return (
    <Card>
      {update ? <SavingProgress /> : false}
      <Box id="parcelplaces" paddingTop={2} paddingBottom={2} paddingLeft={2} paddingRight={2}>
        <Typography variant="h3" marginBottom={4}>
          Výdejní místa, která nelze používat.
        </Typography>
        {isLoading ? (
          <Skeleton height={150} sx={{ transform: "scale(1,1)" }} />
        ) : (
          <Box marginTop={4}>
            <form
              onSubmit={handleSubmit(fields => {
                mutateAsync(fields);
              })}
            >
              <Grid container alignItems={"center"}>
                <Grid item xs={8} display={"flex"} alignContent={"center"}>
                  <Check label={"ParcelBox"} name={"disabledParcelBox"} control={control} />
                </Grid>
                <Grid item xs={4} />
                <Grid item xs={8} display={"flex"} alignContent={"center"}>
                  <Check label={"ParcelShop"} name={"disabledParcelShop"} control={control}/>
                </Grid>
                <Grid item xs={4} />
                <Grid item xs={8} display={"flex"} alignContent={"center"}>
                  <Check label={"AlzaBox"} name={"disabledAlzaBox"} control={control}/>
                </Grid>
                <Grid item xs={4} />
                <Grid item xs={8}>
                  <Button type="submit">Uložit</Button>
                </Grid>
              </Grid>
            </form>
          </Box>
        )}
      </Box>
    </Card>
  );
};

export default ParcelPlaces;
