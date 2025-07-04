import Box from "@mui/material/Box";
import Typography from "@mui/material/Typography";
import Grid from "@mui/material/Grid";
import HeaderMain from "../components/header/main";
import HeaderPage from "../components/header/page";
import { useNavigate } from "react-router-dom";
import Add from "@mui/icons-material/Add";
import HeaderButton from "../components/HeaderButton";
import useHeaderStyle from "./useHeaderStyle";
import { useCollections } from "../queries/useCollectionQueries";
import Balik2 from "../assets/balik2.svg";
import imagePath from "../assets/imagePath";
import { Link } from "@mui/material";
import Card from "@mui/material/Card";
import CollectionGrid from "../components/datagrids/CollectionGrid";

const CollectionsPage = () => {
  const navigate = useNavigate();
  const { classes } = useHeaderStyle();

  const { data, isLoading } = useCollections();

  return (
    <>
      <HeaderMain />
      <HeaderPage
        left={
          <Typography component={"h1"} className={classes.h1}>
            Svozy
          </Typography>
        }
        right={
          <Box display={"flex"} alignContent={"center"}>
            <Box alignSelf={"center"}>
              <HeaderButton
                onClick={() => {
                  navigate("/collection/new")
                }}
              >
                <Add style={{ color: "gray" }} />
                &nbsp;<strong>Objednat svoz</strong>
              </HeaderButton>
            </Box>
          </Box>
        }
      />
      {(!data || data?.length === 0) && !isLoading ?
          <>
              <Box pl={2} pr={2} pt={16}>
                <Grid container alignContent={"center"} alignItems={"center"} textAlign={"center"}>
                  <Grid item xs={12}>
                    <img src={imagePath(Balik2)} />
                  </Grid>


                        <Grid item xs={12}>
                        <Typography variant="body1" >Vyhledávanému výrazu neodpovídá žádná objednávka</Typography>
                        </Grid>

                  <Grid item xs={12}>
                    <Link
                      href="#"
                      onClick={e => {
                        e.preventDefault();
                        navigate("/collection/new")
                      }}
                      color={"primary"}
                      fontWeight={700}
                      style={{ textDecoration: "none" }}
                    >
                      Objednat svoz
                    </Link>
                  </Grid>
                </Grid>
              </Box>{" "}
          </>
      :
        <Box pl={2} pr={2} justifyContent="center" display={"flex"}>
            <Grid maxWidth={"xl"} pt={4} pb={4} alignContent={"center"} height={"100%"} spacing={0} container >
                <Grid item xs={12}>
                    <Card  style={{ height: 685 }} >
                        <CollectionGrid data={data} isLoading={isLoading} />
                    </Card>
                </Grid>
            </Grid>
        </Box>}
    </>
  );
};

export default CollectionsPage;
