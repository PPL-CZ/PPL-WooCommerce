import { Fragment, MutableRefObject, useCallback, useRef, useState } from "react";
import { components } from "../../../schema";
import IconButton from "@mui/material/IconButton";
import TableCell from "@mui/material/TableCell";
import TableRow from "@mui/material/TableRow";
import { UseFieldArrayMove, useFormContext, useFormState } from "react-hook-form";
import WindowIcon from "@mui/icons-material/OpenInBrowser";
import { useTableStyle } from "./styles";
import {
  useRefreshBatch,
  useRemoveShipmentFromBatch,
  useReorderShipmentInBatch,
} from "../../../queries/useBatchQueries";
import CreateShipmentWidget from "../../widgets/CreateShipmentWidget";
import Package from "./package";
import { makeOrderUrl } from "../../../connection";
import { useDrag, useDrop } from "react-dnd";
import { ItemActionsMenu } from "./ItemActionsMenu";
import { tableConfig } from "./tableConfig";

type ShipmentWithAdditionalModel = components["schemas"]["ShipmentWithAdditionalModel"];

const Item = (props: {
  batchId: string;
  maxRows: number;
  position: number;
  flashId: number | null;
  isLocked: boolean;
  hideOrderAnchor?: boolean;
  setFlashId: (flashId: number | null) => void;
  move: UseFieldArrayMove;
  draggedPosition: MutableRefObject<number | null>;
}) => {
  const {
    classes: { trError, hoverSelected, dragUsed },
  } = useTableStyle();

  const reorderShipmentInBatch = useReorderShipmentInBatch(props.batchId);
  const removeShipmentFromBatch = useRemoveShipmentFromBatch(props.batchId);
  const refreshBatch = useRefreshBatch(props.batchId);

  const [edit, setEdit] = useState(0);

  const { watch, getValues } = useFormContext<{ items: ShipmentWithAdditionalModel[] }>();
  const { errors } = useFormState();

  let className = "";

  // @ts-ignore
  if (errors.items?.[props.position]) {
    className = trError;
  }
  if (props.flashId === props.position) className += " " + hoverSelected;
  if (props.draggedPosition.current === props.position) className += " " + dragUsed;

  const model = watch(`items.${props.position}`);

  const basicData = model;
  const recipient = model.shipment.recipient;
  const parcel = model.shipment.parcel;
  let cod = false;

  const packages = (() => {
    const packages = model.shipment.packages;
    if (!packages?.[0]?.shipmentNumber) {
      return packages?.length ?? 0;
    }

    return packages.map((x, index) => (
      <Package
        key={x.id!}
        index={index}
        batchRemoteId={model.shipment.batchRemoteId}
        batchId={props.batchId}
        shipmentId={`${model.shipment.id}`}
        package={x}
      />
    ));
  })();

  const newPosition = useCallback(
    (newPosition: number, finish: boolean) => {
      if (!finish) {
        props.setFlashId(props.position);
      } else {
        props.move(newPosition, props.position);
        const ids = getValues("items").map(x => `${x.shipment.id}`);
        reorderShipmentInBatch.mutateAsync({
          shipment_id: ids,
        });
        props.setFlashId(null);
      }
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [props.position]
  );

  const handleRef = useRef<any>(null);

  const DND_TYPE = "ROW";

  const ref = useRef<HTMLTableRowElement | null>(null);

  const [, drag] = useDrag(
    () => ({
      type: DND_TYPE,
      item: () => {
        if (props.draggedPosition.current === null) props.draggedPosition.current = props.position;
        return { index: props.position };
      },
      collect: monitor => ({ isDragging: monitor.isDragging() }),
    }),
    [props.position]
  );

  const [, drop] = useDrop(
    () => ({
      accept: DND_TYPE,
      hover: (item: { index: number }, monitor) => {
        if (!ref.current) return;
        const dragIndex = item.index;
        const hoverIndex = props.position;

        props.setFlashId(dragIndex!);
        if (props.position === dragIndex) return;

        newPosition(dragIndex, false);
        item.index = hoverIndex;
      },
      drop: (item, monitor) => {
        if (!monitor.didDrop()) {
          newPosition(props.draggedPosition.current!, true);
          props.draggedPosition.current = null;
        }
      },
    }),
    [newPosition]
  );

  drop(ref);
  drag(handleRef);

  const style = useTableStyle();

  return (
    <TableRow ref={ref} className={className}>
      <TableCell sx={tableConfig.body.cell}>
        {edit ? (
          <CreateShipmentWidget
            shipment={model.shipment}
            onFinish={() => {
              setEdit(0);
              refreshBatch();
            }}
          />
        ) : null}
        {model.shipment.lock || props.isLocked ? null : (
          <>
            <button className={style.classes.draggable} ref={handleRef}>
              ⋮⋮
            </button>{" "}
          </>
        )}
        {basicData.shipment.orderId}
        &nbsp;&nbsp;
        {model.shipment.orderId && props.hideOrderAnchor !== false ? (
          <IconButton
            size={tableConfig.icon.size}
            onClick={e => {
              e.preventDefault();
              const url = makeOrderUrl(`${model.shipment.orderId}`);
              window.open(url);
            }}
          >
            <WindowIcon fontSize={tableConfig.icon.fontSize} />
          </IconButton>
        ) : null}{" "}
        {basicData.shipment.id ? `(${basicData.shipment.id})` : null}
      </TableCell>
      <TableCell sx={tableConfig.body.cell}>
        {basicData.shipment.hasParcel
          ? (() => {
              const parcelAddress = [
                (parcel?.name || "") + " " + (parcel?.name2 || ""),
                parcel?.street,
                (parcel?.zip || "") + " " + (parcel?.city + ""),
              ]
                .filter(x => x && x.trim())
                .map((x, index) => {
                  return (
                    <Fragment key={index}>
                      {x}
                      <br />
                    </Fragment>
                  );
                });
              if (parcelAddress) return <address>{parcelAddress}</address>;
              return null;
            })()
          : (() => {
              const recepientAddress = [
                recipient?.name,
                recipient?.contact,
                recipient?.street,
                (recipient?.zip || "") + " " + (recipient?.city + ""),
              ]
                .filter(x => x && x.trim())
                .map((x, index) => {
                  return (
                    <Fragment key={index}>
                      {x}
                      <br />
                    </Fragment>
                  );
                });
              if (recepientAddress.length) return <address>{recepientAddress}</address>;
              return null;
            })()}
      </TableCell>
      <TableCell sx={tableConfig.body.cell}>{basicData.shipment.serviceName}</TableCell>
      <TableCell sx={tableConfig.body.cell}>{packages}</TableCell>
      <TableCell sx={tableConfig.body.cell}>
        {!cod && basicData.shipment.codValue
          ? basicData.shipment.codValue + (basicData.shipment.codValueCurrency || "")
          : ""}
      </TableCell>
      <TableCell sx={tableConfig.body.cell}>{basicData.shipment.codVariableNumber || ""}</TableCell>
      {model.shipment.lock || props.isLocked ? null : (
        <TableCell sx={tableConfig.body.cell}>
          <ItemActionsMenu
            position={props.position}
            isLocked={!!model.shipment.lock || props.isLocked}
            onRemove={() => {
              removeShipmentFromBatch.mutateAsync({
                shipment_id: model.shipment.id!,
              });
            }}
            onShowDetail={() => {
              setEdit(model.shipment.id!);
            }}
          />
        </TableCell>
      )}
    </TableRow>
  );
};

export default Item;
