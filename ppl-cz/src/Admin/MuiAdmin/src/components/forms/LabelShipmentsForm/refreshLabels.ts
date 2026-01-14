import { UseFormSetValue } from "react-hook-form";
import { fetchRefreshLabels } from "../../../queries/useLabelQueries";

import { CreteLabelShipmentItems } from "./types";
import {makePrintUrl} from "../../../connection";
import {components} from "../../../schema";

type RefreshShipmentBatchReturnModel = components["schemas"]["RefreshShipmentBatchReturnModel"];

export const refreshLabels = async (
  batchId: string,
  shipmentId: number[],
  printForm: string,
  controller: AbortController,
  setValue: UseFormSetValue<CreteLabelShipmentItems>
): Promise<[URL, RefreshShipmentBatchReturnModel ]|null> => {
  try {
    while (true) {
      const result = await fetchRefreshLabels(batchId, shipmentId, controller);

      if (result.status === 200 && result.data) {
        result.data.shipments?.forEach((x, index) => {
          // @ts-ignore
          setValue(`items.${index}.shipment`, x);
        });

        if (result.data.batchs?.length
            && result.data.shipments?.some(x => x.packages?.some(x => x.shipmentNumber || x.importError))
        ) {
          const a = document.createElement("a");
          a.href = result.data.batchs[0];
          return [new URL( makePrintUrl(result.data.batchs[0], null, null, printForm)), result.data];
        }
      }

      await new Promise<void>((res, rej) => {
        let timeout: any = null;
        let abort = (success: any) => {
          clearTimeout(timeout);
          controller.signal.removeEventListener("abort", abort);
          success === true ? res() : rej();
        };
        timeout = setTimeout(() => abort(true), 5000);
        controller.signal.addEventListener("abort", abort);
      });
    }
  } catch (e) {
    return null;
  }
};

export default refreshLabels;
