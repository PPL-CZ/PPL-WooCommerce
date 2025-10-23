import { components } from "../../../schema";

type ShipmentWithAdditionalModel = components["schemas"]["ShipmentWithAdditionalModel"];

export type CreteLabelShipmentItems = {
  labelPrintSetting: string;
  items: ShipmentWithAdditionalModel[];
};
