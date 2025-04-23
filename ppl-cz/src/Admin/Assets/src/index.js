import "./product/tab";
import InitParcelShop from "./order/parcelshop";
import { panel as InitOrderPanel } from "./order/panel";

import  "./reset-styles.scss";

import InitCategoryTab from "./category/init";
import InitProductTab from "./product/init";
import InitOrderTable from "./order/table";
import InitSettingShipment from "./shipment/shipmentSetting";


window.PPLczPlugin = window.PPLczPlugin ||  [];

const PPLczPlugin = window.PPLczPlugin;

PPLczPlugin.pplczInitCategoryTab = InitCategoryTab;
PPLczPlugin.pplczInitProductTab = InitProductTab;
PPLczPlugin.pplczInitOrderPanel = InitOrderPanel;
PPLczPlugin.pplczPPLParcelshop = InitParcelShop;
PPLczPlugin.pplczInitOrderTable = InitOrderTable;
PPLczPlugin.pplczInitSettingShipment = InitSettingShipment;




