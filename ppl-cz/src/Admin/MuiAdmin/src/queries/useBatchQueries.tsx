import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { baseConnectionUrl } from "../connection";

import { components } from "../schema";

type BatchModel = components["schemas"]["BatchModel"];
type ShipmentWithAdditionalModel = components["schemas"]["ShipmentWithAdditionalModel"];
type PrepareShipmentBatchModel = components["schemas"]["PrepareShipmentBatchModel"];

export const useBatchs = (onlyfree: boolean = false) => {
  return useQuery({
    queryKey: ["batchs-" + (onlyfree ? "free" : "all")],
    retry: (count, error) => {
      return count < 3;
    },
    queryFn: async () => {
      const baseUrl = baseConnectionUrl();
      const free = onlyfree ? "?free=1" : "";

      const url = new URL(`${baseUrl.url}/ppl-cz/v1/shipment/batch`);
      if (free) url.searchParams.set("free", "1");

      const data = await fetch(url, {
        method: "GET",
        headers: {
          "X-WP-nonce": baseUrl.nonce,
        },
      }).then(x => x.json());

      return data as BatchModel[];
    },
  });
};

export const useBatchShipment = (batchId: string) => {
  return useQuery({
    queryKey: ["batchs-" + batchId],
    retry: (count, error) => {
      return count < 3;
    },
    queryFn: async () => {
      if (!batchId) return [];
      const baseUrl = baseConnectionUrl();
      const data = await fetch(`${baseUrl.url}/ppl-cz/v1/shipment/batch/${batchId}/shipment`, {
        method: "GET",
        headers: {
          "X-WP-nonce": baseUrl.nonce,
        },
      }).then(x => x.text());

      const jsondata = JSON.parse(data);
      jsondata.refresher = new Date();
      return jsondata as ShipmentWithAdditionalModel[];
    },
  });
};

export const useRemoveShipmentFromBatch = (batchId: string) => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationKey: ["batchs-remove-" + batchId],
    mutationFn: async (variables: { shipment_id: number }) => {
      const baseUrl = baseConnectionUrl();
      await fetch(`${baseUrl.url}/ppl-cz/v1/shipment/batch/${batchId}/shipment/${variables.shipment_id}`, {
        method: "DELETE",
        headers: {
          "X-WP-nonce": baseUrl.nonce,
        },
      });
    },
    onSuccess: (data, error, context) => {
      queryClient.invalidateQueries({ queryKey: ["batchs-" + batchId] });
    },
  });
};

export const useRefreshBatch = (batchId: string) => {
  const queryClient = useQueryClient();
  return () => {
    return queryClient.refetchQueries({ queryKey: ["batchs-" + batchId] });

  }
};

export const useReorderShipmentInBatch = (batchId: string) => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationKey: ["batchs-reorder-" + batchId],
    mutationFn: async (variables: { shipment_id: (string | number)[] }) => {
      const baseUrl = baseConnectionUrl();
      await fetch(`${baseUrl.url}/ppl-cz/v1/shipment/batch/${batchId}/reorder`, {
        method: "PUT",
        headers: {
          "X-WP-nonce": baseUrl.nonce,
          "Content-Type": "application/json",
        },
        body: JSON.stringify(variables.shipment_id),
      });
    },
    onSuccess: (data, error, context) => {
      queryClient.invalidateQueries({ queryKey: ["batchs-" + batchId] });
    },
  });
};

export const useCancelShipment = (batchId: string) => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationKey: ["batchs-cancel-" + batchId],
    mutationFn: async (variables: { shipmentId: number | string; packageId: number | string }) => {
      const baseUrl = baseConnectionUrl();
      await fetch(`${baseUrl.url}/ppl-cz/v1/shipment/${variables.shipmentId}/cancel/${variables.packageId}`, {
        method: "DELETE",
        headers: {
          "X-WP-nonce": baseUrl.nonce,
        },
      });
    },
    onSuccess: (data, error, context) => {
      queryClient.invalidateQueries({ queryKey: ["batchs-" + batchId] });
    },
  });
};

export const useTestState = (batchId: string) => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationKey: ["batchs-test-" + batchId],
    mutationFn: async (variables: { shipmentId: number | string; packageId: number | string }) => {
      const baseUrl = baseConnectionUrl();
      await fetch(`${baseUrl.url}/ppl-cz/v1/shipment/${variables.shipmentId}/state/${variables.packageId}`, {
        method: "PUT",
        headers: {
          "X-WP-nonce": baseUrl.nonce,
        },
      });
    },
    onSuccess: (data, error, context) => {
      queryClient.invalidateQueries({ queryKey: ["batchs-" + batchId] });
    },
  });
};

export const useCreateBatch = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ["batch-create"],
    mutationFn: async () => {
      const baseUrl = baseConnectionUrl();
      const url = `${baseUrl.url}/ppl-cz/v1/shipment/batch`;
      const response = await fetch(url, {
        method: "POST",
        headers: {
          "X-WP-nonce": baseUrl.nonce,
        },
      });
      const id = response.headers.get("location")?.split("/").reverse()[0];
      return id;
    },
    onSuccess: (data, error, context) => {
      queryClient.invalidateQueries({ queryKey: ["batchs-free", "batchs-all"] });
    },
  });
};

export const useAddShipments = () => {
  const queryClient = useQueryClient();

  const addBatch = useCreateBatch();

  return useMutation({
    mutationKey: ["batch-add-shipment"],
    mutationFn: async (variables: { batchId: string | undefined; items: PrepareShipmentBatchModel }) => {
      let { batchId, items } = variables;

      if (!batchId) batchId = await addBatch.mutateAsync();

      const baseUrl = baseConnectionUrl();
      const url = `${baseUrl.url}/ppl-cz/v1/shipment/batch/${batchId}/shipment`;

      await fetch(url, {
        method: "PUT",
        headers: {
          "X-WP-nonce": baseUrl.nonce,
          "Content-Type": "application/json",
        },
        body: JSON.stringify(items),
      });

      return batchId;
    },
    onSuccess: (data, error, context) => {
      queryClient.invalidateQueries({ queryKey: ["batchs-" + data] });
    },
  });
};
