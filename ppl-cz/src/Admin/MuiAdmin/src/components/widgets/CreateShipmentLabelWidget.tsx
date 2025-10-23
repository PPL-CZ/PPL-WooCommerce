import { components } from "../../schema";
import LabelShipmentForm from "../forms/LabelShipmentsForm";
import { useAddShipments, useBatchShipment } from "../../queries/useBatchQueries";
import { useEffect, useState } from "react";
import SavingProgress from "../SavingProgress";
import Dialog from "@mui/material/Dialog";
import DialogTitle from "@mui/material/DialogTitle";
import IconButton from "@mui/material/IconButton";
import CloseIcon from "@mui/icons-material/Close";
type ShipmentModel = components["schemas"]["ShipmentModel"];

const CreateShipmentLabelWidget = (props: {
  shipments: { shipment: ShipmentModel; errors: any }[];
  hideOrderAnchor?: boolean;
  onFinish?: () => void;
  onRefresh?: (orderIds: number[]) => void;
}) => {
  const [batchId, setBatchId] = useState("");

  const addShipments = useAddShipments();
  useEffect(() => {
    addShipments
      .mutateAsync({
        batchId: undefined,
        items: {
          items: props.shipments.map(x => {
            if (x.shipment.id)
              return {
                shipmentId: x.shipment.id,
              };
            return {
              orderId: x.shipment.orderId!,
            };
          }),
        },
      })
      .then(x => {
        if (x) setBatchId(x);
      });
  }, []);

  const { data } = useBatchShipment(batchId);

  return (
    <>
      {(!batchId || !data) && <SavingProgress />}
      {batchId && (
        <Dialog open={!!batchId} maxWidth={"xl"}>
          <DialogTitle>
            <IconButton
              onClick={props.onFinish}
              sx={{
                position: "absolute",
                right: 8,
                top: 8,
              }}
            >
              <CloseIcon />
            </IconButton>
          </DialogTitle>
          {data ? (
            <LabelShipmentForm
              batchId={batchId}
              hideOrderAnchor={props.hideOrderAnchor}
              models={data}
              onFinish={() => props.onFinish?.()}
              onRefresh={ids => props.onRefresh?.(ids)}
            />
          ) : null}
        </Dialog>
      )}
    </>
  );
};

export default CreateShipmentLabelWidget;
