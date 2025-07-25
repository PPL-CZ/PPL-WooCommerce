import { useQuery, useQueryClient } from "@tanstack/react-query";
import { baseConnectionUrl } from "../connection";
import { components } from "../schema";

type SendErrorLogModel = components['schemas']['SendErrorLogModel'];
type ErrorLogItemModel = components['schemas']['ErrorLogItemModel'];

export const deleteLog = (data: ErrorLogItemModel) => {
    const baseUrl = baseConnectionUrl();
    return fetch(`${baseUrl.url}/ppl-cz/v1/logs/${data.id}`, {
        method: "DELETE",
        headers: {
            "X-WP-Nonce": baseUrl.nonce,
            "Content-Type": "application/json"
        }
    })
}

export const sendLog = (data: SendErrorLogModel) => {
    const baseUrl = baseConnectionUrl();
    return fetch(`${baseUrl.url}/ppl-cz/v1/logs/send`, {
        method: "POST",
        headers: {
            "X-WP-Nonce": baseUrl.nonce,
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    }).then(x => {
        switch (x.status)
        {
            case 204:
                return null;
            case 400:
                return x.json();
        }
        throw new Error("Problém s odesláním")
    });
}

export const useLogs = (product_ids: string[], order_ids: string[]) => {

    const ids = product_ids.join(',');
    const ids2 = order_ids.join(',')
    return useQuery({
        queryKey: ["logs", ids, ids2 ],
        retry: (count, error) => {
            return count < 3;
        },
        queryFn: async () => {
            const baseUrl = baseConnectionUrl();

            const url = new URL(`${baseUrl.url}/ppl-cz/v1/logs`);
            if (ids) {
                url.searchParams.set("product_ids", ids);
            }
            if (ids2)
                url.searchParams.set("order_ids", ids2);

            const data = await fetch(url, {
                headers: {
                    "X-WP-Nonce": baseUrl.nonce,
                },
            }).then(x => x.json());

            return data as components["schemas"]["ErrorLogModel"];
        },
    });
};
