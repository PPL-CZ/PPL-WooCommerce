export const baseConnectionUrl = (): {
  nonce: "string";
  url: "string";
} => {
  // @ts-ignore
  return window["pplcz_data"];
};
