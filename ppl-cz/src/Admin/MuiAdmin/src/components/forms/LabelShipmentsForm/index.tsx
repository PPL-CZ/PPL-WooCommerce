import Box from "@mui/material/Box";
import { components } from "../../../schema";
import { useLabelPrintSettingQuery, useLabelPrintSettingMutation } from "../../../queries/settings";
import { useQueryLabelPrint } from "../../../queries/codelists";
import { FormProvider, useFieldArray, useForm } from "react-hook-form";
import { useEffect, useMemo } from "react";
import SavingProgress from "../../SavingProgress";
import { usePrintUrl } from "./hooks/usePrintUrl";
import { useShipmentReordering } from "./hooks/useShipmentReordering";
import { useLabelCreationProcess } from "./hooks/useLabelCreationProcess";
import { LabelCreationStepper } from "./LabelCreationStepper";
import { PrintFormatSelector } from "./PrintFormatSelector";
import { ShipmentsTable } from "./ShipmentsTable";
import { CreteLabelShipmentItems } from "./types";

type ShipmentWithAdditionalModel = components["schemas"]["ShipmentWithAdditionalModel"];

export const LabelShipmentForm = (props: {
  batchId: string;
  models: ShipmentWithAdditionalModel[];
  hideOrderAnchor?: boolean;
  onFinish?: () => void;
  onRefresh?: (orderIds: number[]) => void;
}) => {
  const currentPrint = useLabelPrintSettingQuery();
  const availableValues = useQueryLabelPrint();
  const labelPrintMutation = useLabelPrintSettingMutation();

  const values = useMemo(() => {
    return {
      labelPrintSetting: currentPrint.data || "",
      items: props.models,
    } as CreteLabelShipmentItems;
  }, [props.models, currentPrint.data]);

  const form = useForm<CreteLabelShipmentItems>({
    values,
  });

  const { getValues, setError, watch, clearErrors, setValue, control } = form;
  const printState = watch("labelPrintSetting");

  const labelCreationProcess = useLabelCreationProcess({
    batchId: props.batchId,
    printState,
    getValues,
    setError,
    setValue,
    clearErrors,
    onRefresh: props.onRefresh,
  });

  const printUrl = usePrintUrl(props.models, printState, labelCreationProcess.url);

  const reordering = useShipmentReordering(props.batchId, getValues);

  const { fields, move } = useFieldArray({
    control,
    name: "items",
  });

  // Initialize print setting
  useEffect(() => {
    if (currentPrint.data) {
      form.setValue("labelPrintSetting", currentPrint.data);
    }
  }, [currentPrint.data, form]);

  // Initialize errors from models
  useEffect(() => {
    const tim = setTimeout(() => {
        props.models.forEach((x, index) => {
          if (x.errors?.length ) {
            const error = x.errors.shift();
            setError(`items.${index}`, {
              type: "server",
              message: error?.values[0],
            });
          }
          if (x.shipment.importErrors?.length)
          {
            const error = x.shipment.importErrors;
            setError(`items.${index}`, {
              type: "server",
              message: error[0],
            });
          }
        });
    }, 500);

    return () => {
      clearTimeout(tim);
    }
  }, [values, props.models, setError]);

  // Set locked state
  useEffect(() => {
    if (
      props.models[0]?.shipment.lock &&
      (!props.models[0]?.shipment.batchRemoteId || !props.models.every(x => x.shipment.packages?.every(y => y.shipmentNumber || y.importError)))
    ) {
      labelCreationProcess.setLocked(true);
    }
  }, [props.models, labelCreationProcess]);

  const handleCreate = () => {
    labelPrintMutation.mutateAsync({
      printState,
    });
    labelCreationProcess.setCreate(true);
  };

  return (
    <>
      {reordering.loading ? <SavingProgress /> : null}
      <FormProvider {...form}>
        <LabelCreationStepper
          step={labelCreationProcess.step}
          errorMessage={labelCreationProcess.errorMessage}
          message={labelCreationProcess.message}
          printUrl={printUrl}
        />
        <Box className="modalBox">
          <PrintFormatSelector
            printUrl={printUrl}
            availablePrinters={availableValues.data || []}
            isCreating={labelCreationProcess.create || labelCreationProcess.locked}
            errorMessage={labelCreationProcess.errorMessage}
            onCreate={handleCreate}
          />
          <ShipmentsTable
            batchId={props.batchId}
            fields={fields}
            move={move}
            isLocked={!!props.models[0]?.shipment.lock || labelCreationProcess.locked || labelCreationProcess.create}
            hideOrderAnchor={props.hideOrderAnchor}
            onReorder={reordering.reorder}
          />
        </Box>
      </FormProvider>
    </>
  );
};

export default LabelShipmentForm;
