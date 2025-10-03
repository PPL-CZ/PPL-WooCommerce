import Box from "@mui/material/Box";
import Grid from "@mui/material/Grid";

import { useHeaderStyle } from "./styles";
import React from "react";
import Left from "../Left";
import Right from "../Right";

const HeaderPage = (props: { left: React.ReactNode; right?: React.ReactNode }) => {
  const { classes } = useHeaderStyle();

  return (
    <Box className={classes.pageBar} justifyContent="center" display={"flex"}>
      <Grid maxWidth={"xl"} alignContent={"center"} alignItems={"center"} height={"100%"} spacing={0} container>
        <Grid item xs={6} alignSelf={"center"}>
          <Left>{props.left}</Left>
        </Grid>
        {props.right ? (
          <Grid item xs={6} justifyContent={"right"} display={"flex"}>
            <Right>{props.right}</Right>
          </Grid>
        ) : null}
      </Grid>
    </Box>
  );
};

export default HeaderPage;
