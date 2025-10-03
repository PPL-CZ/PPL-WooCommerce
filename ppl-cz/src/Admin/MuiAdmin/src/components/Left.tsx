import Box from "@mui/material/Box";
import { ReactNode } from "react";

const Left = (props: { children: ReactNode }) => {
  return <Box marginLeft={2}>{props.children}</Box>;
};
export default Left;
