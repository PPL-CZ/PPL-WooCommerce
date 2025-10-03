import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { components } from "../schema";
import { baseConnectionUrl } from "../connection";
import { UnknownErrorException, ValidationErrorException } from "./types";

type SenderAddressModel = components["schemas"]["SenderAddressModel"];
type SyncPhasesModel = components["schemas"]["SyncPhasesModel"];

export const useSenderAddressesQuery = () => {
  const { data } = useQuery({
    queryKey: ["sender-addresses"],
    queryFn: () => {
      const defs = baseConnectionUrl();
      return fetch(`${defs.url}/ppl-cz/v1/setting/sender-addresses`, {
        headers: {
          "X-WP-Nonce": defs.nonce,
        },
      }).then(x => x.json() as Promise<SenderAddressModel[]>);
    },
  });
  return data;
};

export const useSenderAddressesMutation = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationKey: ["sender-addresses"],
    mutationFn: (data: SenderAddressModel[]) => {
      const defs = baseConnectionUrl();
      return fetch(`${defs.url}/ppl-cz/v1/setting/sender-addresses`, {
        method: "PUT",
        headers: {
          "X-WP-Nonce": defs.nonce,
          "content-type": "application/json",
        },
        body: JSON.stringify(data),
      }).then(async x => {
        if (x.status === 400) {
          const data = await x.json();
          throw new ValidationErrorException(x.status, data.data);
        } else if (x.status > 400) throw new UnknownErrorException(x.status);
        return x;
      });
    },
    onSuccess: () => {
      qc.refetchQueries({
        queryKey: ["sender-addresses"],
      });
    },
  });
};


export const useLabelPrintSettingQuery = () => {
  return useQuery({
    queryKey: ["print-setting"],
    queryFn: async () => {
      const conn = baseConnectionUrl();
      return fetch(`${conn.url}/ppl-cz/v1/setting/print`, {
        method: "GET",
        headers: {
          "X-WP-Nonce": conn.nonce,
        },
      }).then(x => x.json() as Promise<string>);
    },
  });
};

export const useQueryShipmentStates = () =>
  useQuery({
    queryKey: ["phase-shipments"],
    queryFn: () => {
      const conn = baseConnectionUrl();
      return fetch(`${conn.url}/ppl-cz/v1/setting/shipment-phases`, {
        method: "GET",
        headers: {
          "X-WP-Nonce": conn.nonce,
        },
      }).then(x => x.json() as Promise<SyncPhasesModel>);
    },
  });
