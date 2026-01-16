import Box from "@mui/material/Box";
import { components } from "../../../schema";
import { useLabelPrintSettingQuery, useLabelPrintSettingMutation } from "../../../queries/settings";
import { useQueryLabelPrint } from "../../../queries/codelists";
import { FormProvider, useFieldArray, useForm, FieldErrors } from "react-hook-form";
import {useCallback, useEffect, useMemo, useRef} from "react";
import SavingProgress from "../../SavingProgress";
import { usePrintUrl } from "./hooks/usePrintUrl";
import { useShipmentReordering } from "./hooks/useShipmentReordering";
import { useLabelCreationProcess } from "./hooks/useLabelCreationProcess";
import { LabelCreationStepper } from "./LabelCreationStepper";
import { PrintFormatSelector } from "./PrintFormatSelector";
import { ShipmentsTable } from "./ShipmentsTable";
import { CreteLabelShipmentItems } from "./types";

type ShipmentWithAdditionalModel = components["schemas"]["ShipmentWithAdditionalModel"];


const useShipmentResolver = (models: ShipmentWithAdditionalModel[], print: string | undefined) => {

  const data = useMemo(() => {
    const errors: any = {};

    models.forEach((x, index) => {
      if (x.errors?.length) {
        errors[`items.${index}`] = x.errors[0]?.values[0]; // bez shift()
      }

      if (x.shipment.importErrors?.length) {
        errors[`items.${index}`] = x.shipment.importErrors[0];
      }
    });

    return {
      labelPrintSetting: print || "",
      items: models,
    } as CreteLabelShipmentItems;

  }, [models, print]);

  const resolver = useCallback(
      (values: CreteLabelShipmentItems, context: any, options: any) => {
        const errors: any = {};

        values.items.forEach((x, index) => {  // použij values, ne models
          if (x.errors?.length) {
            errors.items ??= {};
            errors.items[index] = x.errors[0]?.values[0];
          }

          if (x.shipment.importErrors?.length) {
            errors.items ??= {};
            errors.items[index] = x.shipment.importErrors[0];
          }
        });

        return {
          values,
          errors
        } as { values: CreteLabelShipmentItems, errors: FieldErrors<CreteLabelShipmentItems> };
      },
      [] // pokud používáš values, nepotřebuješ models v deps
  );

  return {
    values: data,
    resolver,
  };
};


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

  const {resolver, values} = useShipmentResolver(props.models, currentPrint.data);

  const form = useForm<CreteLabelShipmentItems>({
    resolver,
    values
  });

  const { getValues, setError, watch, clearErrors, setValue, control, trigger } = form;
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

  useEffect(() => {
    requestAnimationFrame(() => trigger());
  }, [values]);

  // Set locked state
  useEffect(() => {
    if (
        props.models[0]?.shipment.lock &&
        (!props.models[0]?.shipment.batchRemoteId || !props.models[0]?.shipment?.packages?.[0].shipmentNumber)
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
                isCreating={labelCreationProcess.create || !!labelCreationProcess.locked}
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
