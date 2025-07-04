import {UseFormSetError, UseFormSetValue} from "react-hook-form";
import {baseConnectionUrl} from "../../../connection";
import {ValidationError} from "../../../queries/types";
import {components} from "../../../schema";

type PrepareShipmentBatchItemModel = components["schemas"]["PrepareShipmentBatchItemModel"];
type ShipmentModel = components["schemas"]["ShipmentModel"];
type PrepareShipmentBatchReturnModel = components["schemas"]["PrepareShipmentBatchReturnModel"];

type CreteLabelShipmentItems = {
    labelPrintSetting: string;
    items: ShipmentModel[];
};


export const prepareShipments = async (
    values: PrepareShipmentBatchItemModel[],
    controller: AbortController,
    setError: UseFormSetError<CreteLabelShipmentItems>,
    setValue: UseFormSetValue<CreteLabelShipmentItems>,
) => {
    const conn = baseConnectionUrl();

    const response = await fetch(`${conn.url}/ppl-cz/v1/shipment/batch/preparing`, {
        headers: {
            "X-WP-Nonce": conn.nonce,
            "Content-Type": "application/json",
        },
        signal: controller.signal,
        method: "POST",
        body: JSON.stringify({ items: values }),

    });

    if (response.status === 200)
    {
        const ret = (await response.json()) as PrepareShipmentBatchReturnModel;
        ret.shipmentId?.forEach((x, index) => {
            setValue(`items.${index}.id`, x);
        });
        return true;
    }
    else if (response.status === 400)
    {
        const validationError = (await response.json()).data as ValidationError;
        Object.keys(validationError.errors).forEach(key => {
            const error = validationError.errors[key];
            const validKey = key.replace(/^item\.([0-9]+)/, item => {
                const num = item.match(/[0-9]+/)!;
                return `item.${parseInt(num[0])}`;
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