import { useQuery } from "@tanstack/react-query";
import { baseConnectionUrl } from "../connection";

import { components } from "../schema";

type ShipmentMethodModel = components["schemas"]["ShipmentMethodModel"];
type CurrencyModel = components["schemas"]["CurrencyModel"];
type CountryModel = components["schemas"]["CountryModel"];
type LabelPrintModel = components["schemas"]["LabelPrintModel"];

export const useQueryShipmentMethods = () => {
  const { data } = useQuery({
    queryKey: ["methodsCodelist"],
    queryFn: () => {
      const defs = baseConnectionUrl();
      return fetch(`${defs.url}/ppl-cz/v1/codelist/methods`, {
        headers: {
          "X-WP-Nonce": defs.nonce,
        },
      }).then(x => x.json() as Promise<ShipmentMethodModel[]>);
    },
  });

  return data;
};

export const useQueryCurrencies = () => {
  const { data } = useQuery({
    queryKey: ["currenciesCodelist"],
    queryFn: () => {
      const defs = baseConnectionUrl();
      return fetch(`${defs.url}/ppl-cz/v1/codelist/currencies`, {
        headers: {
          "X-WP-Nonce": defs.nonce,
        },
      }).then(x => x.json() as Promise<CurrencyModel[]>);
    },
  });

  return data;
};

export const useQueryCountries = () => {
  const { data } = useQuery({
    queryKey: ["countriesCodelist"],
    queryFn: () => {
      const defs = baseConnectionUrl();
      return fetch(`${defs.url}/ppl-cz/v1/codelist/countries`, {
        headers: {
          "X-WP-Nonce": defs.nonce,
        },
      }).then(x => x.json() as Promise<CountryModel[]>);
    },
  });

  return data;
};

export const useQueryLabelPrint = () =>
  useQuery({
    queryKey: ["print-setting-printers"],
    queryFn: () => {
      const conn = baseConnectionUrl();
      return fetch(`${conn.url}/ppl-cz/v1/setting/available-printers`, {
        method: "GET",
        headers: {
          "X-WP-Nonce": conn.nonce,
        },
      }).then(x => x.json() as Promise<LabelPrintModel[]>);
    },
  });
