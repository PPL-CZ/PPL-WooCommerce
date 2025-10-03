import Box from "@mui/material/Box";
import { ReactNode } from "react";

const Right = (props: { children: ReactNode; alignRight?: boolean }) => {
  return <Box marginRight={2}>{props.children}</Box>;
};
export default Right;
