import { handlePackageAction } from './utils';

/**
 * Odstranění zásilky
 */
export const removeShipment = (nonce: string, orderId: number, shipmentId?: number): void => {
    handlePackageAction('pplcz_order_panel_remove_shipment', nonce, orderId, shipmentId);
};

/**
 * Přidání balíčku do zásilky
 */
export const addPackage = (nonce: string, orderId: number, shipmentId?: number): void => {
    handlePackageAction('pplcz_order_panel_add_package', nonce, orderId, shipmentId);
};

/**
 * Odstranění balíčku ze zásilky
 */
export const removePackage = (nonce: string, orderId: number, shipmentId?: number): void => {
    handlePackageAction('pplcz_order_panel_remove_package', nonce, orderId, shipmentId);
};

/**
 * Zrušení balíčku
 */
export const cancelPackage = (nonce: string, orderId: number, shipmentId: number, packageId: number): void => {
    handlePackageAction('pplcz_order_panel_cancel_package', nonce, orderId, shipmentId, packageId);
};