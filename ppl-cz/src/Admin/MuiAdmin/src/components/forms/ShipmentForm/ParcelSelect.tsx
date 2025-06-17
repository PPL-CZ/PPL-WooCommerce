import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import IconButton from "@mui/material/IconButton";
import { components } from "../../../schema";
import { useState } from "react";
import { baseConnectionUrl } from "../../../connection";
import MapIcon from "@mui/icons-material/Map";
import SavingProgress from "../../SavingProgress";

type ParcelAddressModel = components["schemas"]["ParcelAddressModel"];
type UpdateShipmentParcelModel = components["schemas"]["UpdateShipmentParcelModel"];
type ShipmentModel = components["schemas"]["ShipmentModel"];

const ParcelSelect = (props: {
  parcelshop?: ParcelAddressModel;

  onChange: (id: number) => void;
  error?: string;
  shipment: ShipmentModel;
  onFinish?: () => void;
}) => {
  const [loading, setLoading] = useState(false);

  const fetcher = (accessPoint: any) => {
    const nonce = baseConnectionUrl();
    return fetch(`${nonce.url}/ppl-cz/v1/shipment/${props.shipment.id}/parcel`, {
      method: "PUT",
      headers: {
        "X-WP-nonce": nonce.nonce,
        "content-type": "application/json",
      },
      body: JSON.stringify({
        parcelCode: accessPoint.code,
      } as UpdateShipmentParcelModel),
    });
  };

  const onClick = () => {
    const recipient = props.shipment.recipient;
    const map: { address?: string; country?: string; parcelShop?: boolean } = {};
    map.address = [recipient?.street, recipient?.city, recipient?.city].filter(x => x).join(", ");
    if (props.shipment.recipient?.country) map.country = props.shipment.recipient.country;
    if (props.shipment.age) map.parcelShop = true;

    // @ts-ignore
    PplMap(accessPoint => {
      if (accessPoint) {
        setLoading(true);
        fetcher(accessPoint)
          .then(() => {
            props.onChange(props.shipment.id!);
            setLoading(false);
          })
          .catch(e => {
            setLoading(false);
          });
      }
    }, map);
  };

  return (
    <>
      {loading ? <SavingProgress /> : null}
      <Box className="modalBox" p={2}>
        {!props.parcelshop ? (
          <>
            Parcelshop neurčen{" "}
            <IconButton onClick={onClick}>
              <MapIcon />
            </IconButton>
          </>
        ) : (
          <div>
            <address>
              {props.parcelshop.name}{" "}
              <IconButton onClick={onClick}>
                <MapIcon />
              </IconButton>
              <br />
              {props.parcelshop?.name2?.trim() ? (
                <>
                  {props.parcelshop.name2}
                  <br />
                </>
              ) : null}
              {props.parcelshop.street}
              <br />
              {props.parcelshop.city}
              <br />
            </address>
          </div>
        )}
        <br />
        <Button
          onClick={e => {
            setLoading(true);
            e.preventDefault();
            props.onFinish?.();
          }}
        >
          Zavřít
        </Button>
      </Box>
    </>
  );
};

export default ParcelSelect;
