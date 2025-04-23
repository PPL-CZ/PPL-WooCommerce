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
import { Controller, FormProvider, UseFormSetError, UseFormSetValue, useFieldArray, useForm } from "react-hook-form";
import { useEffect, useState } from "react";
import { baseConnectionUrl } from "../../../connection";
import { ValidationError } from "../../../queries/types";
import SelectPrint from "../Inputs/SelectPrint";

type ShipmentModel = components["schemas"]["ShipmentModel"];
type PrepareShipmentBatchItemModel = components["schemas"]["PrepareShipmentBatchItemModel"];
type PrepareShipmentBatchReturnModel = components["schemas"]["PrepareShipmentBatchReturnModel"];
type CreateShipmentLabelBatchModel = components["schemas"]["CreateShipmentLabelBatchModel"];
type RefreshShipmentBatchReturnModel = components["schemas"]["RefreshShipmentBatchReturnModel"];

type CreteLabelShipmentItems = {
  labelPrintSetting: string;
  items: ShipmentModel[];
};

const reducerPrepareShipment = (acc: PrepareShipmentBatchItemModel[][], shipment: ShipmentModel) => {
  let pos = acc.length;
  if (pos === 0) {
    acc.push([]);
  } else {
    pos--;
    if (acc[pos].length >= 2) {
      acc.push([]);
      pos++;
    }
  }
  acc[pos].push({
    orderId: shipment.orderId,
    shipmentId: shipment.id,
  });
  return acc;
};

const refreshLabels = (
  shipmentId: number[],
  printForm: string,
  controller: AbortController,
  setValue: UseFormSetValue<CreteLabelShipmentItems>,
  beginPosition: number
) => {
  const conn = baseConnectionUrl();

  return new Promise<void>((res, rej) => {
    let running = false;
    const interval = setInterval(async () => {
      if (running) return;
      running = true;
      await fetch(`${conn.url}/ppl-cz/v1/shipment/batch/refresh-labels`, {
        headers: {
          "X-WP-Nonce": conn.nonce,
          "Content-Type": "application/json",
        },
        signal: controller.signal,
        method: "POST",
        body: JSON.stringify({ shipmentId } as CreateShipmentLabelBatchModel),
      })
        .then(async x => {
          if (x.status === 200) {
            const ret = (await x.json()) as RefreshShipmentBatchReturnModel;
            ret.shipments?.forEach((x, index) => {
              // @ts-ignore
              setValue(`item.${index + beginPosition}`, x);
            });

            if (ret.batchs?.length) {
              clearInterval(interval);
              ret.batchs.forEach(x => {
                // @ts-ignore
                const url = new URL(pplcz_data.file_download_url);
                url.searchParams.append("pplcz_download", x);
                url.searchParams.append("pplcz_print", printForm);
                window.open(url, "_self");
              });
              res();
            }
          }
        })
        .finally(() => {
          running = false;
        });
    }, 2000);

    controller.signal.addEventListener("abort", () => {
      clearInterval(interval);
      rej();
    });
  });
};

const createLabels = (
  shipmentId: number[],
  printSetting: string,
  controller: AbortController,
  setError: UseFormSetError<CreteLabelShipmentItems>,
  setValue: UseFormSetValue<CreteLabelShipmentItems>,
  beginPosition: number
) => {
  const conn = baseConnectionUrl();

  return fetch(`${conn.url}/ppl-cz/v1/shipment/batch/create-labels`, {
    headers: {
      "X-WP-Nonce": conn.nonce,
      "Content-Type": "application/json",
    },
    signal: controller.signal,
    method: "POST",
    body: JSON.stringify({ printSetting, shipmentId } as CreateShipmentLabelBatchModel),
  }).then(async x => {
    if (x.status === 200 || x.status === 400) {
      const ret = (await x.json()) as ShipmentModel[];
      for (let i = 0; i < ret.length; i++) {
        // @ts-ignore
        setValue(`item.${beginPosition + i}`, ret[i]);
        if (x.status === 400) {
          // @ts-ignore
          setError(`item.${beginPosition + i}`, { message: "Chyba při importu" });
        }
      }
      if (x.status === 200) return true;
      return false;
    }
    if (x.status === 204) return true;

    throw new Error("Problém s přípravou zásilek");
  });
};

const prepareShipments = (
  values: PrepareShipmentBatchItemModel[],
  controller: AbortController,
  setError: UseFormSetError<CreteLabelShipmentItems>,
  setValue: UseFormSetValue<CreteLabelShipmentItems>,
  beginPosition: number
) => {
  const conn = baseConnectionUrl();

  return fetch(`${conn.url}/ppl-cz/v1/shipment/batch/preparing`, {
    headers: {
      "X-WP-Nonce": conn.nonce,
      "Content-Type": "application/json",
    },
    signal: controller.signal,
    method: "POST",
    body: JSON.stringify({ items: values }),
  }).then(async x => {
    if (x.status === 200) {
      const ret = (await x.json()) as PrepareShipmentBatchReturnModel;
      ret.shipmentId?.forEach((x, index) => {
        setValue(`items.${index + beginPosition}.id`, x);
      });
      return true;
    } else if (x.status === 400) {
      const validationError = (await x.json()).data as ValidationError;
      Object.keys(validationError.errors).forEach(key => {
        const error = validationError.errors[key];
        const validKey = key.replace(/^item\.([0-9]+)/, item => {
          const num = item.match(/[0-9]+/)!;
          return `item.${parseInt(num[0]) + beginPosition}`;
        });

        // @ts-ignore
        setError(validKey, {
          type: "server",
          message: error[0],
        });
      });
      return false;
    }
    throw new Error("Problém s přípravou zásilek");
  });
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

  const formCtx = useForm<CreteLabelShipmentItems>({
    values: {
      labelPrintSetting: current.data || "",
      items: props.models.map(x => x.shipment),
    },
  });

  const [create, setCreate] = useState(false);

  const { getValues, setError, clearErrors, setValue } = formCtx;

  const [errorMessage, setErrorMessage] = useState("");

  useEffect(() => {
    if (current.data) {
      setTimeout(() => {
        formCtx.setValue("labelPrintSetting", current.data);
      }, 100);
    }
  }, [current.data || ""]);

  useEffect(() => {
    if (create) {
      const controller = new AbortController();
      let values = formCtx.getValues("items");
      clearErrors();
      setErrorMessage("");

      (async () => {
        let prepared = values.reduce(reducerPrepareShipment, [] as PrepareShipmentBatchItemModel[][]);
        let begin = 0;

        for (const item of prepared) {
          try {
            if (await prepareShipments(item, controller, setError, setValue, begin)) {
              props.onRefresh?.(
                item
                  .map(x => x.orderId)
                  .filter(x => !!x)
                  .map(x => x!)
              );
              begin += item.length;
            } else {
              setCreate(false);
              setErrorMessage("Při validaci zásilek došlo k chybě");
              return;
            }
          } catch (e) {
            setCreate(false);
          }
        }

        setStep(1);
        values = formCtx.getValues("items");
        try {
          await createLabels(
            values.map(x => x.id!),
            getValues("labelPrintSetting"),
            controller,
            setError,
            setValue,
            begin
          );
        } catch (e) {
          props.onRefresh?.(
            values
              .map(x => x.orderId)
              .filter(x => !!x)
              .map(x => x!)
          );
          setErrorMessage("Při vytváření zásilek došlo k chybě");
          setCreate(false);
          return;
        }

        setStep(2);

        try {
          await refreshLabels(
            values.map(x => x.id!),
            getValues("labelPrintSetting"),
            controller,
            setValue,
            begin
          );
          props.onRefresh?.(
            values
              .map(x => x.orderId)
              .filter(x => !!x)
              .map(x => x!)
          );
        } catch (e) {
          props.onRefresh?.(
            values
              .map(x => x.orderId)
              .filter(x => !!x)
              .map(x => x!)
          );
          setCreate(false);
          setErrorMessage("Při získávání skupinového tisku došlo k chybě");
        }
      })();
    }
  }, [create]);

  const { control } = formCtx;
  const { fields } = useFieldArray({
    control,
    name: "items",
  });
  return (
    <>
      <FormProvider {...formCtx}>
        <Box p={2}>
          {step >= -1 ? (
              <Box p={2}>
                <Stepper activeStep={1}>
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
        </Box>
        <Box className="modalBox">
          <Box p={2}>
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
