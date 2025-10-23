import { useEffect, useState } from "react";
import { UseFormClearErrors, UseFormGetValues, UseFormSetError, UseFormSetValue } from "react-hook-form";
import { useRefreshBatch } from "../../../../queries/useBatchQueries";
import { CreteLabelShipmentItems } from "../types";
import createLabels from "../createLabels";
import prepareShipments from "../prepareShipments";
import refreshLabels from "../refreshLabels";

interface UseLabelCreationProcessProps {
  batchId: string;
  printState: string;
  getValues: UseFormGetValues<CreteLabelShipmentItems>;
  setError: UseFormSetError<CreteLabelShipmentItems>;
  setValue: UseFormSetValue<CreteLabelShipmentItems>;
  clearErrors: UseFormClearErrors<CreteLabelShipmentItems>;
  onRefresh?: (orderIds: number[]) => void;
}

export const useLabelCreationProcess = ({
  batchId,
  printState,
  getValues,
  setError,
  setValue,
  clearErrors,
  onRefresh,
}: UseLabelCreationProcessProps) => {
  const [step, setStep] = useState(-1);
  const [create, setCreate] = useState(false);
  const [locked, setLocked] = useState(false);
  const [url, setUrl] = useState<URL | null>(null);
  const [errorMessage, setErrorMessage] = useState("");
  const [message, setMessage] = useState("");

  const refreshBatch = useRefreshBatch(batchId);

  useEffect(() => {
    if (create || locked) {
      const controller = new AbortController();
      let values = getValues("items");

      clearErrors();
      setErrorMessage("");

      (async () => {
        if (create) {
          setStep(1);
          setMessage("Probíhá validace zásilek");
          let prepared = values.map(shipment => ({
            orderId: shipment.shipment.orderId,
            shipmentId: shipment.shipment.id,
          }));

          try {
            if (await prepareShipments(batchId, prepared, controller, setError, setValue)) {
              onRefresh?.(prepared.map(x => x?.orderId || 0).filter(x => !!x));
            } else {
              setCreate(false);
              setErrorMessage("Při validaci zásilek došlo k chybě");
              setMessage("");
              return;
            }
          } catch (e) {
            setErrorMessage("Neočekávaný problém");
            setMessage("");
            setCreate(false);
            setLocked(false);
            return;
          }

          setStep(2);
          values = getValues("items");
          setMessage("Probíhá příprava etiket");
          try {
            await createLabels(
              batchId,
              values.map(x => x.shipment.id!),
              printState,
              controller,
              setError,
              setValue
            );
          } catch (e) {
            onRefresh?.(values.map(x => x?.shipment?.orderId || 0).filter(x => !!x));
            setErrorMessage("Při vytváření zásilek došlo k chybě");
            setMessage("");
            setCreate(false);
            setLocked(false);
            return;
          }
        }

        if (create || locked) {
          setStep(3);
          setMessage("Čekáme na vytvoření etiket");
          try {
            const url = await refreshLabels(
              batchId,
              values.map(x => x.shipment.id!),
              printState,
              controller,
              setValue
            );
            await refreshBatch();
            setMessage("");
            if (url) {
              setUrl(url);
            } else {
              setErrorMessage("Při získávání skupinového tisku došlo k chybě");
            }
            onRefresh?.(values.map(x => x?.shipment.orderId || 0).filter(x => !!x));
          } catch (e) {
            onRefresh?.(values.map(x => x?.shipment.orderId || 0).filter(x => !!x));
            setErrorMessage("Při získávání skupinového tisku došlo k chybě");
            setMessage("");
          }
          setCreate(false);
          setLocked(false);
        }
      })();
    }
  }, [create, locked]);

  return {
    step,
    create,
    locked,
    url,
    errorMessage,
    message,
    setCreate,
    setLocked,
  };
};
