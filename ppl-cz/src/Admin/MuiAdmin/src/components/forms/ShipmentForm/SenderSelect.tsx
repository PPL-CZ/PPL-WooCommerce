import { components } from "../../../schema";

import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import CircularProgress from "@mui/material/CircularProgress";

import { useSenderAddressesQuery } from "../../../queries/settings";
import SelectInput from "../Inputs/SelectInput";
import { useState } from "react";
import SavingProgress from "../../SavingProgress";
import { useUpdateShipmentSenderMutation } from "../../../queries/useShipmentQueries";

type UpdateShipmentSenderModel = components["schemas"]["UpdateShipmentSenderModel"];
type SenderAddressModel = components["schemas"]["SenderAddressModel"];

const SenderSelect = (props: {
  sender?: SenderAddressModel;
  onChange: (id: number) => void;
  shipmentId: number;
  error?: string;
  onFinish?: () => void;
}) => {
  const data = useSenderAddressesQuery();
  const [sender, setSender] = useState(props.sender);
  const [disabled, setDisabled] = useState(false);
  const { mutateAsync } = useUpdateShipmentSenderMutation((shipmentId) => {
    props.onChange(shipmentId);
  });

  if (!data) {
    return <CircularProgress />;
  }

  const optionals = data.map(x => {
    return {
      label: x.addressName ?? "Bez názvu",
      id: `${x.id}` ?? '',
      data: x,
    };
  });

  return (
    <>
      {disabled ? <SavingProgress /> : null}
      <Box className="modalBox" p={2}>
        <SelectInput
          optionals={optionals}
          value={`${sender?.id ?? ""}`}
          onChange={val => {
            const newSender = optionals.filter(x => `${x.id}` === val)[0];
            if (newSender) {
              setSender(newSender.data);
              mutateAsync({
                shipmentId: props.shipmentId,
                senderId: parseInt(newSender.id),
              });
            }
          }}
          error={props.error}
        />
        {!sender ? null : (
          <Box p={2}>
            <address>
              {sender.addressName}
              <br />
              {sender.street}
              <br />
              {sender.zip} {sender.city}
              <br />
              {sender.note}
              <br />
            </address>
          </Box>
        )}
      </Box>
      <hr />
      <Box p={2}>
        <Button
          onClick={e => {
            setDisabled(true);
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

export default SenderSelect;
