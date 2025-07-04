import {UseFormSetValue} from "react-hook-form";
import {baseConnectionUrl} from "../../../connection";
import {components} from "../../../schema";

type CreateShipmentLabelBatchModel = components["schemas"]["CreateShipmentLabelBatchModel"];
type ShipmentModel = components["schemas"]["ShipmentModel"];
type RefreshShipmentBatchReturnModel = components["schemas"]["RefreshShipmentBatchReturnModel"];

type CreteLabelShipmentItems = {
    labelPrintSetting: string;
    items: ShipmentModel[];
};


export const refreshLabels =  async (
    shipmentId: number[],
    printForm: string,
    controller: AbortController,
    setValue: UseFormSetValue<CreteLabelShipmentItems>,
) => {
    const conn = baseConnectionUrl();

    try {
        while (true) {
            const response = await fetch(`${conn.url}/ppl-cz/v1/shipment/batch/refresh-labels`, {
                headers: {
                    "X-WP-Nonce": conn.nonce,
                    "Content-Type": "application/json",
                },
                signal: controller.signal,
                method: "POST",
                body: JSON.stringify({shipmentId} as CreateShipmentLabelBatchModel),
            });

            if (response.status === 200) {
                const retModel = await response.json() as RefreshShipmentBatchReturnModel;
                retModel.shipments?.forEach((x, index) => {
                    // @ts-ignore
                    setValue(`item.${index}`, x);
                });

                if (retModel.batchs?.length) {
                    // @ts-ignore
                    const url = new URL(pplcz_data.file_download_url);
                    url.searchParams.append("pplcz_download", retModel.batchs?.[0]);
                    url.searchParams.append("pplcz_print", printForm);
                    return url
                }
            }

            await new Promise<void>((res, rej) => {
                let timeout: any = null;
                let abort = (success: any) => {
                    clearTimeout(timeout);
                    controller.signal.removeEventListener("abort", abort);
                    success === true ? res() : rej();
                }
                timeout = setTimeout(() =>  abort(true), 5000);
                controller.signal.addEventListener("abort", abort);

            });
        }
    }
    catch (e)
    {
        return null;
    }
};

export default refreshLabels;