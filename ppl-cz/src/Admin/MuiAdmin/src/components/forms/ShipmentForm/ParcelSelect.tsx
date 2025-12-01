import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import IconButton from "@mui/material/IconButton";
import { components } from "../../../schema";
import { useState } from "react";
import MapIcon from "@mui/icons-material/Map";
import SavingProgress from "../../SavingProgress";
import SaveInfo from "./SaveInfo";
import { useUpdateShipmentParcelMutation } from "../../../queries/useShipmentQueries";
import {useQueryShipmentMethods} from "../../../queries/codelists";

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
  const [error, setError] = useState(0);
  const { mutateAsync } = useUpdateShipmentParcelMutation((shipmentId) => {
    props.onChange(shipmentId);
  });

  const data = useQueryShipmentMethods();

  const onClick = () => {
    const recipient = props.shipment.recipient;
    const map: { address?: string; country?: string; parcelShop?: boolean, hiddenPoints?: string[] } = {};
    map.address = [recipient?.street, recipient?.city, recipient?.city].filter(x => x).join(", ");
    if (props.shipment.recipient?.country) map.country = props.shipment.recipient.country;
    if (props.shipment.age) map.parcelShop = true;

    if (data)
    {
        const disabledParcelTypes = data.find(x => x.code === props.shipment.serviceCode)?.disabledParcelTypes;
        if (disabledParcelTypes) {
            map.hiddenPoints = disabledParcelTypes;
        }
    }



    // @ts-ignore
    PplMap(async (accessPoint) => {
      if (accessPoint) {
        setLoading(true);
        try {
          await mutateAsync({
            shipmentId: props.shipment.id!,
            parcelCode: accessPoint.code,
          });
        } catch (e: any) {
          if (e?.message === "Parcel not found") {
            setError(err => err + 1);
          }
        } finally {
          setLoading(false);
        }
      }
    }, map);
  };

  return (
    <>
      {loading ? <SavingProgress /> : null}
      {error? <SaveInfo key={error} timeout={5000} color={"error"}>Box/obchod nebyl nalezen</SaveInfo>: null}
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
