export const baseConnectionUrl = (): {
  nonce: "string";
  url: "string";
} => {
  // @ts-ignore
  return window["pplcz_data"];
};

export const makePrintUrl = (
  batchRemoteId: string,
  shipmentId: string | null,
  packageId?: string | null,
  print?: string | null
) => {
  const a = document.createElement("a");
  a.href = `${document.location}`;

  const url = new URL(a.href);
  url.search = "";
  url.hash = "";
  url.searchParams.set("page", "pplcz_filedownload");
  url.searchParams.set("pplcz_remote_batch", batchRemoteId);
  if (shipmentId) {
    url.searchParams.set("pplcz_shipment", shipmentId);
    if (packageId) url.searchParams.set("pplcz_package", packageId);
  }
  if (print) url.searchParams.set("pplcz_print", print);
  return `${url}`;
};

export const makeOrderUrl = (orderId: string) => {
  const a = document.createElement("a");
  a.href = `${document.location}`;
  // @ts-ignore
  const newOrder = !window["pplcz_data"].old_order_url;
  if (newOrder) {
    const url = new URL(a.href);
    url.pathname = `/wp-admin/admin.php`;
    url.search = "";
    url.hash = "";
    url.searchParams.set("page", "wc-orders");
    url.searchParams.set("action", "edit");
    url.searchParams.set("id", `${orderId}`);
    return `${url}`;
  } else {
    const url = new URL(a.href);
    url.pathname = `/wp-admin/post.php`;
    url.search = "";
    url.hash = "";
    url.searchParams.set("post", `${orderId}`);
    url.searchParams.set("action", "edit");
    return `${url}`;
  }
};
