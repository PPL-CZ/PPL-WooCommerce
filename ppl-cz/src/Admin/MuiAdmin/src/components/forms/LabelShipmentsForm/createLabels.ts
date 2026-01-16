import { UseFormSetError, UseFormSetValue } from "react-hook-form";
import { fetchCreateLabels } from "../../../queries/useLabelQueries";

import { CreteLabelShipmentItems } from "./types";

const createLabels = async (
  batchId: string,
  shipmentId: number[],
  printSetting: string,
  controller: AbortController,
  setError: UseFormSetError<CreteLabelShipmentItems>,
  setValue: UseFormSetValue<CreteLabelShipmentItems>
) => {
  const result = await fetchCreateLabels(batchId, shipmentId, printSetting, controller);

  if ((result.status === 200 || result.status === 400) && result.data) {
    for (let i = 0; i < result.data.length; i++) {
      // @ts-ignore
      setValue(`items.${i}.shipment`, result.data[i]);
      if (result.status === 400) {
        // @ts-ignore
        setError(`items.${i}`, { message: "Chyba při importu" });
      }
    }

    return result.status !== 400;
  }

  if (result.status === 204) return true;

  if (result.status === 500)
    throw new Error(result.error);

  throw new Error("Problém s přípravou zásilek");
};

export default createLabels;
