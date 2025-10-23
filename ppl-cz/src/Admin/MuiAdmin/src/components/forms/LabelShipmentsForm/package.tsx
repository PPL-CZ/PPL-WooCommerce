import { components } from "../../../schema";
import { useState } from "react";
import { Link, List, ListItemButton, Menu } from "@mui/material";
import { useCancelShipment, useTestState } from "../../../queries/useBatchQueries";
import { makePrintUrl } from "../../../connection";
import { useFormContext } from "react-hook-form";

type PackageModel = components["schemas"]["PackageModel"];
type ShipmentWithAdditionalModel = components["schemas"]["ShipmentWithAdditionalModel"];

type CreteLabelShipmentItems = {
  labelPrintSetting: string;
  items: ShipmentWithAdditionalModel[];
};

const Package = (props: {
  batchId: string;
  batchRemoteId?: string | null;
  shipmentId: string;
  package: PackageModel;
  index: number;
}) => {
  const [showMenu, setShowMenu] = useState(false);
  const [anchorEl, setAnchorEl] = useState<HTMLElement | null>(null);

  const cancelShipment = useCancelShipment(props.batchId);
  const testShipemnt = useTestState(props.batchId);

  const { watch } = useFormContext<CreteLabelShipmentItems>();
  const print = watch("labelPrintSetting");

  if (!props.package.shipmentNumber || !props.batchRemoteId) return null;

  return (
    <>
      <Link
        href={"#"}
        onClick={e => {
          e.preventDefault();
          setAnchorEl(e.target as HTMLElement);
          setShowMenu(true);
        }}
      >
        {props.package.shipmentNumber} ({props.package.phaseLabel || "Objednáno"})
      </Link>
      <div>
        <Menu
          anchorEl={anchorEl}
          id="popover"
          className="wp-reset-div"
          open={showMenu}
          onClose={() => {
            setShowMenu(false);
          }}
        >
          <List component="nav">
            {props.package.phase === "Order" || props.package.phase === "None" || !props.package.phase?.trim() ? (
              <ListItemButton
                onClick={e => {
                  cancelShipment.mutate({
                    shipmentId: props.shipmentId,
                    packageId: props.package.id!,
                  });
                  setShowMenu(false);
                }}
              >
                Zrušit
              </ListItemButton>
            ) : null}
            <ListItemButton
              onClick={e => {
                testShipemnt.mutate({
                  shipmentId: props.shipmentId,
                  packageId: props.package.id!,
                });
                setShowMenu(false);
              }}
            >
              Zjistit stav
            </ListItemButton>
            <ListItemButton
              onClick={e => {
                setShowMenu(false);
                window.open(makePrintUrl(props.batchRemoteId!, props.shipmentId, `${props.package.id}`, print));
              }}
            >
              Tisk
            </ListItemButton>
            {!props.index ? (
              <ListItemButton
                onClick={e => {
                  setShowMenu(false);
                  window.open(makePrintUrl(props.batchRemoteId!, props.shipmentId, null, print));
                }}
              >
                Tisk všech
              </ListItemButton>
            ) : null}
          </List>
        </Menu>
      </div>
    </>
  );
};

export default Package;
