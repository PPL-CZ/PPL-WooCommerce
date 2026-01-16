import { baseConnectionUrl } from "../connection";
import { components } from "../schema";

type PrepareShipmentBatchItemModel = components["schemas"]["PrepareShipmentBatchItemModel"];
type PrepareShipmentBatchReturnModel = components["schemas"]["PrepareShipmentBatchReturnModel"];
type CreateShipmentLabelBatchModel = components["schemas"]["CreateShipmentLabelBatchModel"];
type RefreshShipmentBatchReturnModel = components["schemas"]["RefreshShipmentBatchReturnModel"];
type ShipmentModel = components["schemas"]["ShipmentModel"];

/**
 * Fetch funkce pro přípravu zásilek v dávce
 */
export const fetchPrepareShipments = async (
  batchId: string,
  values: PrepareShipmentBatchItemModel[],
  controller: AbortController
): Promise<{ status: number; data?: PrepareShipmentBatchReturnModel; errors?: any }> => {
  const conn = baseConnectionUrl();

  const response = await fetch(`${conn.url}/ppl-cz/v1/shipment/batch/${batchId}/preparing`, {
    headers: {
      "X-WP-Nonce": conn.nonce,
      "Content-Type": "application/json",
    },
    signal: controller.signal,
    method: "POST",
    body: JSON.stringify({ items: values }),
  });

  if (response.status === 200) {
    const data = await response.json();
    return { status: 200, data };
  } else if (response.status === 400) {
    const errors = (await response.json()).data;
    return { status: 400, errors };
  }

  throw new Error("Problém s přípravou zásilek");
};

/**
 * Fetch funkce pro vytvoření štítků
 */
export const fetchCreateLabels = async (
  batchId: string,
  shipmentId: number[],
  printSetting: string,
  controller: AbortController
): Promise<{ status: number; data?: ShipmentModel[], error?: string }> => {
  const conn = baseConnectionUrl();

  const response = await fetch(`${conn.url}/ppl-cz/v1/shipment/batch/${batchId}/create-labels`, {
    headers: {
      "X-WP-Nonce": conn.nonce,
      "Content-Type": "application/json",
    },
    signal: controller.signal,
    method: "POST",
    body: JSON.stringify({ printSetting, shipmentId } as CreateShipmentLabelBatchModel),
  });

  if (response.status === 200 || response.status === 400) {
    const data = await response.json();
    return { status: response.status, data };
  }

  if (response.status === 204) {
    return { status: 204 };
  }

  if (response.status === 500)
  {
    try {
      const r = await response.json();
      return {
        status: 500,
        error: JSON.stringify(r)
      }
    }
    catch (e)
    {

    }
  }

  throw new Error("Problém s vytvořením štítků" );
};

/**
 * Fetch funkce pro refresh štítků
 */
export const fetchRefreshLabels = async (
  batchId: string,
  shipmentId: number[],
  controller: AbortController
): Promise<{ status: number; data?: RefreshShipmentBatchReturnModel }> => {
  const { url, nonce } = baseConnectionUrl();

  const response = await fetch(`${url}/ppl-cz/v1/shipment/batch/${batchId}/refresh-labels`, {
    headers: {
      "Content-Type": "application/json",
      "X-WP-Nonce": nonce,
    },
    signal: controller.signal,
    method: "POST",
    body: JSON.stringify({ shipmentId } as CreateShipmentLabelBatchModel),
  });

  if (response.status === 200) {
    const data = await response.json();
    return { status: 200, data };
  }

  return { status: response.status };
};
