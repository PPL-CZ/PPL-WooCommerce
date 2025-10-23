import { useMutation, useQueryClient } from "@tanstack/react-query";
import { baseConnectionUrl } from "../connection";

/**
 * Mutation pro objednání/zrušení svozu
 */
export const useCollectionOrderMutation = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ["collection-order"],
    mutationFn: async (variables: { collectionId: string; action: "DELETE" | "PUT" }) => {
      const { nonce, url } = baseConnectionUrl();

      const response = await fetch(`${url}/ppl-cz/v1/collection/${variables.collectionId}/order`, {
        method: variables.action,
        headers: {
          "X-WP-nonce": nonce,
        },
      });

      if (response.status === 204) {
        return true;
      }

      throw new Error("Failed to update collection order");
    },
    onSuccess: () => {
      queryClient.refetchQueries({
        queryKey: ["collections"],
      });
    },
  });
};
