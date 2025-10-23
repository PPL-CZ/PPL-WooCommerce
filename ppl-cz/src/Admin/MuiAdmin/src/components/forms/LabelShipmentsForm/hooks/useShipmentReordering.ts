import { useState } from "react";
import { UseFormGetValues } from "react-hook-form";
import { useReorderShipmentInBatch } from "../../../../queries/useBatchQueries";
import { CreteLabelShipmentItems } from "../types";

export const useShipmentReordering = (
  batchId: string,
  getValues: UseFormGetValues<CreteLabelShipmentItems>
) => {
  const [loading, setLoading] = useState(false);
  const reorderShipmentInBatch = useReorderShipmentInBatch(batchId);

  const reorder = async (reverse: boolean) => {
    const items = getValues("items");
    let shipments = Array.from(items)
      .sort((x, y) => {
        if (x.shipment.orderId! > y.shipment.orderId!) return 1;
        if (x.shipment.orderId! < y.shipment.orderId!) return -1;
        if (x.shipment.id! > y.shipment.id!) return 1;
        if (x.shipment.id! < y.shipment.id!) return -1;
        return 0;
      })
      .map(x => x.shipment.id!);
    if (reverse) shipments = shipments.reverse();
    setLoading(true);
    try {
      await reorderShipmentInBatch.mutateAsync({ shipment_id: shipments });
    } finally {
      setLoading(false);
    }
  };

  return { loading, reorder };
};
