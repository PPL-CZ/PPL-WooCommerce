import Box from "@mui/material/Box";
import Typography from "@mui/material/Typography";
import Grid from "@mui/material/Grid";
import HeaderMain from "../components/header/main";
import HeaderPage from "../components/header/page";
import useHeaderStyle from "./useHeaderStyle";
import { useBatchShipment } from "../queries/useBatchQueries";
import Card from "@mui/material/Card";
import BatchShipmentGrid from "../components/datagrids/BatchShipmentGrid";

const BatchDetailPage = (props: { batchId: string }) => {
  const { classes } = useHeaderStyle();

  const { data, isLoading } = useBatchShipment(props.batchId);

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
              <Card>
                <BatchShipmentGrid batchId={props.batchId} isLoading={isLoading} data={data} />
              </Card>
            </Grid>
          </Grid>
        </Box>
      )}
    </>
  );
};

export default BatchDetailPage;
