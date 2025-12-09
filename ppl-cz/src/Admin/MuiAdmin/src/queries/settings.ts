import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { components } from "../schema";
import { baseConnectionUrl } from "../connection";
import { UnknownErrorException, ValidationErrorException } from "./types";

type SenderAddressModel = components["schemas"]["SenderAddressModel"];
type SyncPhasesModel = components["schemas"]["SyncPhasesModel"];
type GlobalSettingModel = components["schemas"]["GlobalSettingModel"];

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

export const useLabelPrintSettingMutation = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationKey: ["print-setting"],
    mutationFn: async (data: { printState: string }) => {
      const conn = baseConnectionUrl();
      return fetch(`${conn.url}/ppl-cz/v1/setting/print`, {
        method: "POST",
        headers: {
          "content-type": "application/json",
          "X-WP-Nonce": conn.nonce,
        },
        body: JSON.stringify({
          format: data.printState,
        }),
      });
    },
    onSuccess: () => {
      qc.refetchQueries({
        queryKey: [`print-setting`],
      });
    },
  });
};


export const usePrintOrderStatesSettingQuery = () => {
  return useQuery({
    queryKey: ["print-order-states-setting"],
    queryFn: async () => {
      const conn = baseConnectionUrl();
      return fetch(`${conn.url}/ppl-cz/v1/setting/print-order-statuses`, {
        method: "GET",
        headers: {
          "X-WP-Nonce": conn.nonce,
        },
      }).then(x => x.json() as Promise<string[]>);
    },
  });
};

export const usePrintOrderStatesSettingMutation = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationKey: ["print-order-states-setting"],
    mutationFn: async (data: { printState: string }) => {
      const conn = baseConnectionUrl();
      return fetch(`${conn.url}/ppl-cz/v1/setting/print-order-statuses`, {
        method: "POST",
        headers: {
          "content-type": "application/json",
          "X-WP-Nonce": conn.nonce,
        },
        body: JSON.stringify({
          format: data.printState,
        }),
      });
    },
    onSuccess: () => {
      qc.refetchQueries({
        queryKey: [`print-order-states-setting`],
      });
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

type ParcelPlacesModel = components["schemas"]["ParcelPlacesModel"];
type MyApiModel = components["schemas"]["MyApi2"];

/**
 * Query pro získání nastavení výdejních míst
 */
export const useParcelPlacesQuery = () => {
  return useQuery({
    queryKey: ["parcelplaces"],
    queryFn: async () => {
      const baseUrl = baseConnectionUrl();
      return fetch(`${baseUrl.url}/ppl-cz/v1/setting/parcelplaces`, {
        headers: {
          "X-WP-Nonce": baseUrl.nonce,
        },
      }).then(x => x.json() as Promise<ParcelPlacesModel>);
    },
  });
};

/**
 * Mutation pro aktualizaci nastavení výdejních míst
 */
export const useParcelPlacesMutation = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (data: ParcelPlacesModel) => {
      const baseUrl = baseConnectionUrl();
      await fetch(`${baseUrl.url}/ppl-cz/v1/setting/parcelplaces`, {
        method: "PUT",
        headers: {
          "X-WP-Nonce": baseUrl.nonce,
          "content-type": "application/json",
        },
        body: JSON.stringify(data),
      }).then(async x => {
        if (x.status === 204) {
          return true;
        }
        throw new Error("Failed to update parcel places");
      });
    },
    onSuccess: () => {
      queryClient.refetchQueries({
        queryKey: ["parcelplaces"],
      });
    },
  });
};


/**
 * Query pro získání globálního nastavení
 */
export const useGlobalSettingQuery = () => {
  return useQuery({
    queryKey: ["globalsetting"],
    queryFn: async () => {
      const baseUrl = baseConnectionUrl();
      return fetch(`${baseUrl.url}/ppl-cz/v1/setting/global-settings`, {
        headers: {
          "X-WP-Nonce": baseUrl.nonce,
        },
      }).then(x => x.json() as Promise<GlobalSettingModel>);
    },
  });
};

/**
 * Mutation pro aktualizaci globálního nastavení
 */
export const useGlobalSettingMutation = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (data: GlobalSettingModel) => {
      const baseUrl = baseConnectionUrl();
      await fetch(`${baseUrl.url}/ppl-cz/v1/setting/global-settings`, {
        method: "PUT",
        headers: {
          "X-WP-Nonce": baseUrl.nonce,
          "content-type": "application/json",
        },
        body: JSON.stringify(data),
      }).then(async x => {
        if (x.status === 204) {
          return true;
        }
        throw new Error("Failed to update parcel places");
      });
    },
    onSuccess: () => {
      queryClient.refetchQueries({
        queryKey: ["globalsetting"],
      });
    },
  });
};


/**
 * Query pro získání API přístupových údajů
 */
export const useMyApiQuery = () => {
  return useQuery({
    queryKey: ["myapi2"],
    queryFn: async () => {
      const baseUrl = baseConnectionUrl();
      return fetch(`${baseUrl.url}/ppl-cz/v1/setting/api`, {
        headers: {
          "X-WP-Nonce": baseUrl.nonce,
        },
      }).then(x => x.json() as Promise<MyApiModel>);
    },
  });
};

/**
 * Custom error pro API validaci s detaily chyb
 */
export class MyApiError extends Error {
  constructor(public status: number, public errors?: Record<string, string>) {
    super("API Error");
  }
}

/**
 * Mutation pro aktualizaci API přístupových údajů
 */
export const useMyApiMutation = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (data: MyApiModel) => {
      const baseUrl = baseConnectionUrl();
      const response = await fetch(`${baseUrl.url}/ppl-cz/v1/setting/api`, {
        method: "PUT",
        headers: {
          "X-WP-Nonce": baseUrl.nonce,
          "content-type": "application/json",
        },
        body: JSON.stringify(data),
      });

      if (response.status === 400) {
        const message = await response.json();
        throw new MyApiError(400, message.data.errors);
      } else if (response.status === 204) {
        return { success: true };
      }

      throw new MyApiError(response.status);
    },
    onSuccess: () => {
      queryClient.refetchQueries({
        queryKey: ["myapi2"],
      });
    },
  });
};
