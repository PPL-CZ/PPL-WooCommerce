import Box from "@mui/material/Box";
import Tab from "@mui/material/Tab";
import Tabs from "@mui/material/Tabs";
import { components } from "../../../schema";
import { useQueryShipmentMethods } from "../../../queries/codelists";
import { Fragment, useState } from "react";
import BaseInfoForm from "./BaseInfoForm";
import RecipienAddressForm from "./RecipientAddressForm";
import SenderSelect from "./SenderSelect";
import ParcelSelect from "./ParcelSelect";
import { baseConnectionUrl } from "../../../connection";

type ShipmentModel = components["schemas"]["ShipmentModel"];

function TabPanel(props: { children?: React.ReactNode; index: string; value: string }) {
  const { children, value, index, ...other } = props;

  return (
    <div
      role="tabpanel"
      hidden={value !== index}
      id={`simple-tabpanel-${index}`}
      aria-labelledby={`simple-tab-${index}`}
      {...other}
    >
      {value === index && <Box>{children}</Box>}
    </div>
  );
}

const ShipmentForm = (props: { shipment: ShipmentModel; onFinish?: () => void; onChange?: () => void }) => {
  const [shipment, setShipment] = useState(props.shipment);
  const [key, setKey] = useState(1);

  const data = useQueryShipmentMethods();
  const [panel, setPanel] = useState("base");

  const onChange = (id: number) => {
    const { nonce, url } = baseConnectionUrl();
    fetch(`${url}/ppl-cz/v1/shipment/${id}`, {
      headers: {
        "X-WP-nonce": nonce,
      },
    }).then(async x => {
      if (x.status === 200) {
        setShipment(await x.json());
        props.onChange?.();
      }
    });
  };

  const tabs = ["base", "recipient", "sender", "parcel"].map(x => {
    if (x === "base") {
      return [
        <Tab key={x} label="Zásilky" value="base" />,
        <TabPanel key={x} value={x} index={panel}>
          <BaseInfoForm data={shipment} onChange={onChange} onFinish={props.onFinish} />
        </TabPanel>,
      ];
    }

    if (!shipment.id) return null;

    switch (x) {
      case "recipient":
        return [
          <Tab key={`p${x}`} label="Příjemce" value={x} />,
          <TabPanel key={x} value={x} index={panel}>
            <Box
              style={{
                maxHeight: "80vh",
                maxWidth: "90vw",
                overflow: "auto",
              }}
            >
              <RecipienAddressForm
                address={shipment.recipient}
                shipmentId={shipment.id}
                onChange={onChange}
                onFinish={props.onFinish}
              />
            </Box>
          </TabPanel>,
        ];
      case "sender":
        return [
          <Tab key={`p${x}`} label="Odesílatel" value={x} />,
          <TabPanel key={x} value={x} index={panel}>
            <SenderSelect
              sender={shipment.sender}
              shipmentId={shipment.id}
              onChange={onChange}
              onFinish={props.onFinish}
            />
          </TabPanel>,
        ];
      case "parcel":
        if (shipment.serviceCode && data) {
          if (shipment.hasParcel && data.some(x => x.code === shipment.serviceCode && x.parcelRequired))
            return [
              <Tab key={`p${x}`} label="Parcelshop" value={x} />,
              <TabPanel key={x} value={x} index={panel}>
                <ParcelSelect
                  parcelshop={shipment.parcel}
                  shipment={shipment}
                  onChange={onChange}
                  onFinish={props.onFinish}
                />
              </TabPanel>,
            ];
        }
        return null;
      default:
        return null;
    }
  });
  const availableTabs = tabs.filter(x => !!x);

  return (
    <Fragment key={key}>
      <Tabs
        value={panel}
        onChange={(e, newValue) => {
          setPanel(newValue);
        }}
      >
        {availableTabs.map(x => (x || [])[0])}
      </Tabs>
      {availableTabs.map(x => (x || [])[1])}
    </Fragment>
  );
};

export default ShipmentForm;
