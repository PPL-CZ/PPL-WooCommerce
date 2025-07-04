import Alert from "@mui/material/Alert";
import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import FormLabel from "@mui/material/FormLabel";
import Grid from "@mui/material/Grid";
import Step from "@mui/material/Step";
import StepLabel from "@mui/material/StepLabel";
import Stepper from "@mui/material/Stepper";
import Table from "@mui/material/Table";
import TableBody from "@mui/material/TableBody";
import TableCell from "@mui/material/TableCell";
import TableHead from "@mui/material/TableHead";
import TableRow from "@mui/material/TableRow";
import { components } from "../../../schema";
import Item from "./Item";
import { useLabelPrintSettingQuery } from "../../../queries/settings";
import { useQueryLabelPrint } from "../../../queries/codelists";
import { Controller, FormProvider, useFieldArray, useForm } from "react-hook-form";
import createLabels from "./createLabels";
import prepareShipments from "./prepareShipments";
import refreshLabels from "./refreshLabels";
import {useEffect, useState} from "react";
import SelectPrint from "../Inputs/SelectPrint";

type ShipmentModel = components["schemas"]["ShipmentModel"];

type CreteLabelShipmentItems = {
  labelPrintSetting: string;
  items: ShipmentModel[];
};




export const LabelShipmentForm = (props: {
  models: {
    shipment: ShipmentModel;
    errors?: Record<string, string[]>[];
  }[];
  hideOrderAnchor?: boolean;
  onFinish?: () => void;
  onRefresh?: (orderIds: number[]) => void;
}) => {

  const [step, setStep] = useState(-1);
  const current = useLabelPrintSettingQuery();
  const availableValues = useQueryLabelPrint();

  const form = useForm<CreteLabelShipmentItems>({
    values: {
      labelPrintSetting: current.data || "",
      items: props.models.map(x => x.shipment),
    },
  });

  const [create, setCreate] = useState(false);

  const { getValues, setError, clearErrors, setValue } = form;

  const [ url, setUrl ] = useState<URL|null>(null);

  const [errorMessage, setErrorMessage] = useState("");

  const [ message, setMessage ] = useState("");

  useEffect(() => {
    if (current.data) {
      setTimeout(() => {
        form.setValue("labelPrintSetting", current.data);
      }, 100);
    }
  }, [current.data || ""]);

  useEffect(() => {
    if (create) {
      const controller = new AbortController();
      let values = form.getValues("items");
      clearErrors();
      setErrorMessage("");

      (async () => {
        setStep(1);
        setMessage("Probíhá validace zásilek");
        let prepared = values.map(shipment => ({
          orderId: shipment.orderId,
          shipmentId: shipment.id,
        }));

        try {
          if (await prepareShipments(prepared, controller, setError, setValue)) {
            props.onRefresh?.(prepared.map(x => (x?.orderId || 0)).filter(x => !!x))
          } else {
            setCreate(false);
            setErrorMessage("Při validaci zásilek došlo k chybě");
            setMessage("");
            return;
          }
        }
        catch (e)
        {
          setErrorMessage("Neočekávaný problém");
          setMessage("");
          setCreate(false);
        }



        setStep(2);
        values = form.getValues("items");
        setMessage("Probíhá příprava etiket");
        try {
          await createLabels(
            values.map(x => x.id!),
            getValues("labelPrintSetting"),
            controller,
            setError,
            setValue,
          );
        } catch (e) {
          props.onRefresh?.(values.map(x => (x?.orderId || 0)).filter(x => !!x));
          setErrorMessage("Při vytváření zásilek došlo k chybě");
          setMessage("");
          setCreate(false);
          return;
        }


        setStep(3);
        setMessage("Čekáme na vytvoření etiket");
        try {
          const url = await refreshLabels(
            values.map(x => x.id!),
            getValues("labelPrintSetting"),
            controller,
            setValue,
          );
          setMessage("");
          if (url) {
            setUrl(url);
          }
          else
          {
            setErrorMessage("Při získávání skupinového tisku došlo k chybě")
          }

          props.onRefresh?.(values.map(x => (x?.orderId || 0)).filter(x => !!x));

        } catch (e) {
          props.onRefresh?.(values.map(x => (x?.orderId || 0)).filter(x => !!x));
          setErrorMessage("Při získávání skupinového tisku došlo k chybě");
          setMessage("");
        }
      })();
    }
  }, [create]);

  const { control } = form;
  const { fields } = useFieldArray({
    control,
    name: "items",
  });
  return (
    <>
      <FormProvider {...form}>
        <Box p={2}>
          {step >= -1 ? (
              <Box p={2}>
                <Stepper activeStep={step}>
                  <Step key={1}>
                    <StepLabel>Validace</StepLabel>
                  </Step>
                  <Step key={2}>
                    <StepLabel>Vytvoření zásilek</StepLabel>
                  </Step>
                  <Step key={3}>
                    <StepLabel>Příprava etiket</StepLabel>
                  </Step>
                </Stepper>
              </Box>
          ) : null}
          {errorMessage ? <Alert severity="warning">{errorMessage}</Alert> : null}
          {message ?<Alert severity="success">
                {message}
          </Alert>:null}
        </Box>
        <Box className="modalBox">
          <Box p={2}>
            {url ?
              <center>
                  <Button onClick={e=>{
                    window.open(url, "_blank")
                  }}>Stáhnout štítky</Button></center> : <center>
                </center>}
            <Controller
                key={JSON.stringify(availableValues)}
                control={control}
                name="labelPrintSetting"
                render={({ field: { value, onChange } }) => {
                  return (
                      <>
                        <Grid container>
                          <Grid item flexGrow={1}>
                            <FormLabel>Formát tisku</FormLabel>
                            <SelectPrint key={JSON.stringify(availableValues)}
                                         onChange={e => {
                                           onChange(e);
                                         }}
                                         value={value}
                                         optionals={(availableValues.data || [] )}/>
                          </Grid>
                        </Grid>
                      </>
                  );
                }}
            />
          </Box>
          <Table>
            <TableHead>
              <TableRow>
                <TableCell>Obj.</TableCell>
                <TableCell>Adresa</TableCell>
                <TableCell>Služba</TableCell>
                <TableCell>Dobírka</TableCell>
                <TableCell>VS</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {fields.map((field, index) => (
                <Item hideOrderAnchor={props.hideOrderAnchor} position={index} />
              ))}
            </TableBody>
          </Table>
        </Box>
      </FormProvider>
      <Box p={2}>
        <Grid container alignItems={"center"}>
          <Grid xs={6} item textAlign={"left"}>
            <Button
              disabled={create}
              onClick={e => {
                e.preventDefault();
                setCreate(true);
              }}
            >
              {errorMessage ? "Zkusit znovu" : "Vytisknout zásilky"}
            </Button>
          </Grid>
          <Grid xs={6} item textAlign={"right"}>
            <Button
              onClick={e => {
                setCreate(false);
                e.preventDefault();
                props.onFinish?.();
              }}
            >
              Zavřít
            </Button>
          </Grid>
        </Grid>
      </Box>
    </>
  );
};

export default LabelShipmentForm;
