import { useMutation, useQueryClient } from "@tanstack/react-query";
import { baseConnectionUrl } from "../connection";
import { components } from "../schema";
import { UnknownErrorException, ValidationErrorException } from "./types";

type UpdateShipmentModel = components["schemas"]["UpdateShipmentModel"];
type UpdateShipmentSenderModel = components["schemas"]["UpdateShipmentSenderModel"];
type RecipientAddressModel = components["schemas"]["RecipientAddressModel"];
type UpdateShipmentParcelModel = components["schemas"]["UpdateShipmentParcelModel"];
type UpdateSyncPhasesModel = components["schemas"]["UpdateSyncPhasesModel"];

/**
 * Mutation pro vytvoření/úpravu zásilky
 */
export const useUpdateShipmentMutation = (onSuccessCallback?: (shipmentId: number) => void) => {
  return useMutation({
    mutationKey: ["shipment-update"],
    mutationFn: async (variables: { shipmentId?: number; data: UpdateShipmentModel }) => {
      const { url, nonce } = baseConnectionUrl();
      const id = variables.shipmentId ? `/${variables.shipmentId}` : "";

      const response = await fetch(`${url}/ppl-cz/v1/shipment${id}`, {
        method: "PUT",
        headers: {
          "X-WP-nonce": nonce,
          "Content-Type": "application/json",
        },
        body: JSON.stringify(variables.data),
      });

      if (response.status === 400) {
        const data = await response.json();
        throw new ValidationErrorException(response.status, data.data);
      } else if (response.status > 400) {
        throw new UnknownErrorException(response.status);
      }

      if (response.status === 201) {
        const newId = response.headers.get("location")?.split("/").reverse()?.[0];
        return { shipmentId: parseInt(newId!), isNew: true };
      }

      return { shipmentId: variables.shipmentId!, isNew: false };
    },
    onSuccess: (data) => {
      onSuccessCallback?.(data.shipmentId);
    },
  });
};

/**
 * Mutation pro změnu odesílatele zásilky
 */
export const useUpdateShipmentSenderMutation = (onSuccessCallback?: (shipmentId: number) => void) => {
  return useMutation({
    mutationKey: ["shipment-sender-update"],
    mutationFn: async (variables: { shipmentId: number; senderId: number }) => {
      const { url, nonce } = baseConnectionUrl();

      const response = await fetch(`${url}/ppl-cz/v1/shipment/${variables.shipmentId}/sender`, {
        method: "PUT",
        headers: {
          "content-type": "application/json",
          "X-WP-nonce": nonce,
        },
        body: JSON.stringify({
          senderId: variables.senderId,
        } as UpdateShipmentSenderModel),
      });

      if (response.status === 204) {
        return variables.shipmentId;
      }

      throw new Error("Failed to update sender");
    },
    onSuccess: (shipmentId) => {
      onSuccessCallback?.(shipmentId);
    },
  });
};

/**
 * Mutation pro aktualizaci adresy příjemce
 */
export const useUpdateRecipientMutation = (onSuccessCallback?: (shipmentId: number) => void) => {
  return useMutation({
    mutationKey: ["recipient-update"],
    mutationFn: async (variables: { shipmentId: number; address: RecipientAddressModel }) => {
      const { url, nonce } = baseConnectionUrl();

      const response = await fetch(`${url}/ppl-cz/v1/shipment/${variables.shipmentId}/recipient`, {
        method: "PUT",
        headers: {
          "X-WP-nonce": nonce,
          "content-type": "application/json",
        },
        body: JSON.stringify(variables.address),
      });

      if (response.status === 400) {
        const data = await response.json();
        throw new ValidationErrorException(response.status, data.data);
      } else if (response.status > 400) {
        throw new UnknownErrorException(response.status);
      }

      return variables.shipmentId;
    },
    onSuccess: (shipmentId) => {
      onSuccessCallback?.(shipmentId);
    },
  });
};

/**
 * Mutation pro aktualizaci parcelshop/parcelbox zásilky
 */
export const useUpdateShipmentParcelMutation = (onSuccessCallback?: (shipmentId: number) => void) => {
  return useMutation({
    mutationKey: ["shipment-parcel-update"],
    mutationFn: async (variables: { shipmentId: number; parcelCode: string }) => {
      const { url, nonce } = baseConnectionUrl();

      const response = await fetch(`${url}/ppl-cz/v1/shipment/${variables.shipmentId}/parcel`, {
        method: "PUT",
        headers: {
          "X-WP-nonce": nonce,
          "content-type": "application/json",
        },
        body: JSON.stringify({
          parcelCode: variables.parcelCode,
        } as UpdateShipmentParcelModel),
      });

      if (response.status === 404) {
        throw new Error("Parcel not found");
      }

      return variables.shipmentId;
    },
    onSuccess: (shipmentId) => {
      onSuccessCallback?.(shipmentId);
    },
  });
};

/**
 * Mutation pro aktualizaci synchronizačních fází zásilek
 */
export const useUpdateShipmentPhasesMutation = () => {
  return useMutation({
    mutationKey: ["shipment-phases-update"],
    mutationFn: async (data: UpdateSyncPhasesModel) => {
      const { url, nonce } = baseConnectionUrl();

      await fetch(`${url}/ppl-cz/v1/setting/shipment-phases`, {
        method: "PUT",
        headers: {
          "X-WP-Nonce": nonce,
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });
    },
  });
};
