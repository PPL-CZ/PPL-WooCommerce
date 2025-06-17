import { UseFormSetError, UseFormSetValue } from "react-hook-form";
import { baseConnectionUrl } from "../../../connection";
import { components } from "../../../schema";

type ShipmentModel = components["schemas"]["ShipmentModel"];
type CreateShipmentLabelBatchModel = components["schemas"]["CreateShipmentLabelBatchModel"];

type CreteLabelShipmentItems = {
  labelPrintSetting: string;
  items: ShipmentModel[];
};

const createLabels = async (
  shipmentId: number[],
  printSetting: string,
  controller: AbortController,
  setError: UseFormSetError<CreteLabelShipmentItems>,
  setValue: UseFormSetValue<CreteLabelShipmentItems>
) => {
  const conn = baseConnectionUrl();

  const response = await fetch(`${conn.url}/ppl-cz/v1/shipment/batch/create-labels`, {
    headers: {
      "X-WP-Nonce": conn.nonce,
      "Content-Type": "application/json",
    },
    signal: controller.signal,
    method: "POST",
    body: JSON.stringify({ printSetting, shipmentId } as CreateShipmentLabelBatchModel),
  });

  if (response.status === 200 || response.status === 400) {
    const ret = (await response.json()) as ShipmentModel[];
    for (let i = 0; i < ret.length; i++) {
      // @ts-ignore
      setValue(`item.${i}`, ret[i]);
      if (response.status === 400) {
        // @ts-ignore
        setError(`item.${i}`, { message: "Chyba při importu" });
      }
    }

    if (response.status === 400) {
      return false;
    } else {
      return true;
    }
  }
  if (response.status === 204) return true;

  throw new Error("Problém s přípravou zásilek");
};

export default createLabels;
