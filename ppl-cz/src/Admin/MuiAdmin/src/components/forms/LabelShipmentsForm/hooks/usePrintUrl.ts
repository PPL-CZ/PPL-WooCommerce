import { useMemo } from "react";
import { makePrintUrl } from "../../../../connection";
import { components } from "../../../../schema";

type ShipmentWithAdditionalModel = components["schemas"]["ShipmentWithAdditionalModel"];

export const usePrintUrl = (models: ShipmentWithAdditionalModel[], printState: string, generatedUrl: URL | null) => {
  return useMemo(() => {
    if (
      models?.[0]?.shipment?.lock &&
      models[0].shipment.packages?.[0]?.shipmentNumber &&
      models?.[0]?.shipment.batchRemoteId
    ) {
      return new URL(makePrintUrl(models?.[0]?.shipment.batchRemoteId, null, null, printState));
    }

    if (generatedUrl) return generatedUrl;

    return null;
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [`${generatedUrl}`, models, printState]);
};
