import { UseFormSetError, UseFormSetValue } from "react-hook-form";
import { ValidationError } from "../../../queries/types";
import { components } from "../../../schema";
import { fetchPrepareShipments } from "../../../queries/useLabelQueries";

import { CreteLabelShipmentItems } from "./types";

type PrepareShipmentBatchItemModel = components["schemas"]["PrepareShipmentBatchItemModel"];

export const prepareShipments = async (
  batchId: string,
  values: PrepareShipmentBatchItemModel[],
  controller: AbortController,
  setError: UseFormSetError<CreteLabelShipmentItems>,
  setValue: UseFormSetValue<CreteLabelShipmentItems>
) => {
  const result = await fetchPrepareShipments(batchId, values, controller);

  if (result.status === 200 && result.data) {
    result.data.shipmentId?.forEach((x, index) => {
      setValue(`items.${index}.shipment.id`, x);
    });
    return true;
  } else if (result.status === 400 && result.errors) {
    const validationError = result.errors as ValidationError;
    Object.keys(validationError.errors).forEach(key => {
      const error = validationError.errors[key];
      const validKey = key.replace(/^item\.([0-9]+)/, item => {
        const num = item.match(/[0-9]+/)!;
        return `items.${parseInt(num[0])}`;
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
};

export default prepareShipments;
