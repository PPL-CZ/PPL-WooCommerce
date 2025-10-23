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
import copy from "copy-to-clipboard";

import { components } from "../../schema";
import { Controller, useForm } from "react-hook-form";
import { useEffect, useState } from "react";
import SavingProgress from "../SavingProgress";
import { useMyApiQuery, useMyApiMutation, MyApiError } from "../../queries/settings";

type MyApiModel = components["schemas"]["MyApi2"];

const MyApi = () => {
  const { setValue, handleSubmit, control, setError } = useForm<MyApiModel>();
  const [update, setUpdate] = useState(false);
  const [success, setSuccess] = useState(false);
  const { data, isLoading } = useMyApiQuery();

  const { mutateAsync } = useMyApiMutation();

  useEffect(() => {
    if (data) {
      setTimeout(() => {
        setValue("client_id", data.client_id);
        setValue("client_secret", data.client_secret);
      });
    }
  }, [data]);

  return (
    <Card>
      {update ? <SavingProgress /> : false}
      <Box id="api" paddingTop={2} paddingBottom={2} paddingLeft={2} paddingRight={2}>
        <Typography variant="h3" marginBottom={4}>
          Přístupové údaje
        </Typography>
        {isLoading ? (
          <Skeleton height={150} sx={{ transform: "scale(1,1)" }} />
        ) : (
          <Box marginTop={4}>
            <form
              onSubmit={handleSubmit(async fields => {
                setUpdate(true);
                setSuccess(false);

                try {
                  await mutateAsync(fields);
                  setSuccess(true);
                } catch (error) {
                  if (error instanceof MyApiError && error.status === 400 && error.errors) {
                    if (error.errors[""]) setError("client_secret", { message: error.errors[""] });
                    else {
                      setError("client_id", { message: error.errors["clientId"] });
                      setError("client_secret", { message: error.errors["clientSecret"] });
                    }
                  }
                } finally {
                  setUpdate(false);
                }
              })}
            >
              <Typography component={"p"} mt={2} mb={2} color={"secondary"}>
                Pro získání přístupových údajů kontaktujte{" "}
                <a href="mailto:ithelp@ppl.cz">
                  ithelp@ppl.cz{" "}
                  <ContentCopyIcon
                    onClick={e => {
                      e.preventDefault();
                      copy("ithelp@ppl.cz");
                    }}
                    fontSize="small"
                  />
                </a>{" "}
                prosím.
              </Typography>
              {success ? (
                <Box pb={2}>
                  <Alert icon={<CheckIcon fontSize="inherit" />} severity="success" id="alert-success">
                    Zadané údaje jsou v pořádku
                  </Alert>
                </Box>
              ) : null}
              <Grid container alignItems={"center"}>
                <Grid item xs={4} display={"flex"} alignContent={"center"}>
                  <FormLabel>Client id</FormLabel>
                </Grid>
                <Grid item xs={8}>
                  <Controller
                    name="client_id"
                    control={control}
                    render={({ field: { onChange, value }, fieldState: { error } }) => (
                      <TextField
                        id="client-id"
                        name={"client_id"}
                        value={value ?? ""}
                        size="medium"
                        onChange={onChange}
                        error={!!error && error.type !== "sucess"}
                        helperText={error?.message}
                      />
                    )}
                  />
                </Grid>
                <Grid item xs={4}>
                  <FormLabel>Client secret</FormLabel>
                </Grid>
                <Grid item xs={8}>
                  <Controller
                    name="client_secret"
                    control={control}
                    render={({ field: { onChange, value }, fieldState: { error } }) => (
                      <TextField
                        id="client-secret"
                        name={"client_secret"}
                        value={value ?? ""}
                        size="medium"
                        onChange={onChange}
                        error={!!error}
                        helperText={error?.message}
                      />
                    )}
                  />
                </Grid>
                <Grid item xs={4} />
                <Grid item xs={8}>
                  <Button type="submit">Ověřit a uložit</Button>
                </Grid>
              </Grid>
            </form>
          </Box>
        )}
      </Box>
    </Card>
  );
};

export default MyApi;
