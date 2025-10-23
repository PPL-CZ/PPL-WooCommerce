import Box from "@mui/material/Box";
import Typography from "@mui/material/Typography";
import Grid from "@mui/material/Grid";
import HeaderMain from "../components/header/main";
import HeaderPage from "../components/header/page";
import { useNavigate } from "react-router-dom";
import useHeaderStyle from "./useHeaderStyle";
import { useBatchs } from "../queries/useBatchQueries";
import { Link } from "@mui/material";
import Card from "@mui/material/Card";
import SavingProgress from "../components/SavingProgress";

import BatchGrid from "../components/datagrids/BatchGrid";

const BatchPage = () => {
  const navigate = useNavigate();
  const { classes } = useHeaderStyle();

  const { data, isLoading } = useBatchs();

  return (
    <>
      <HeaderMain />
      <HeaderPage
        left={
          <Typography component={"h1"} className={classes.h1}>
            Tisk etiket
          </Typography>
        }
      />
      {(!data || data?.length === 0) && !isLoading ? (
        <>
          <Box pl={2} pr={2} pt={16}>
            <Grid container alignContent={"center"} alignItems={"center"} textAlign={"center"}>
              <Grid item xs={12}>
                <Typography variant="body1">Nebyl nalezen žádný seznam etiket</Typography>
              </Grid>
            </Grid>
          </Box>{" "}
        </>
      ) : (
        <Box pl={2} pr={2} justifyContent="center" display={"flex"}>
          <Grid maxWidth={"xl"} pt={4} pb={4} alignContent={"center"} height={"100%"} spacing={0} container>
            <Grid item xs={12}>
              <BatchGrid isLoading={isLoading} data={data} />
            </Grid>
          </Grid>
        </Box>
      )}
    </>
  );
};

export default BatchPage;
