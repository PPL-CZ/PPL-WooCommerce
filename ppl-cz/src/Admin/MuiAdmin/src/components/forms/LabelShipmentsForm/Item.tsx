import { Fragment } from "react";
import { components } from "../../../schema";
import IconButton from "@mui/material/IconButton";
import TableCell from "@mui/material/TableCell";
import TableRow from "@mui/material/TableRow";
import { useFormContext, useFormState } from "react-hook-form";
import WindowIcon from "@mui/icons-material/OpenInBrowser";
import { useTableStyle } from "./styles";
type ShipmentModel = components["schemas"]["ShipmentModel"];

const Item = (props: { position: number, hideOrderAnchor?: boolean }) => {
  const {
    classes: { trError },
  } = useTableStyle();

  const { watch } = useFormContext<{ items: ShipmentModel[] }>();
  const { errors } = useFormState();

  let className = "";

  // @ts-ignore
  if (errors.item?.[props.position]) {
    className = trError;
  }

  const model = watch(`items.${props.position}`);

  const basicData = model;
  const recipient = model.recipient;
  const parcel = model.parcel;
  const packages = model.packages ?? [];
  let cod = false;

  return (
    <>
      {packages.map(x => {
        const retData = (
          <TableRow className={className}>
            <TableCell>
              {basicData.referenceId}{" "}
              {model.orderId && props.hideOrderAnchor !== false ? (
                <IconButton
                  onClick={e => {
                    e.preventDefault();
                    const loc = `${document.location}`;
                    if (loc.indexOf('edit.php') > -1)
                      window.open(`/wp-admin/post.php?post=${model.orderId}&action=edit`);
                    else
                      window.open(`/wp-admin/admin.php?page=wc-orders&action=edit&id=${model.orderId}`);
                  }}
                >
                  <WindowIcon />
                </IconButton>
              ) : null}{" "}
             {basicData.id ? `(${basicData.id})` : null}
            </TableCell>
            <TableCell>
              {basicData.hasParcel
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
            <TableCell>{basicData.serviceName}</TableCell>
            <TableCell>
              {!cod && basicData.codValue ? basicData.codValue + (basicData.codValueCurrency || "") : ""}
            </TableCell>
            <TableCell>{basicData.codVariableNumber || ""}</TableCell>
          </TableRow>
        );

        cod = true;

        return retData;
      })}
    </>
  );
};

export default Item;
