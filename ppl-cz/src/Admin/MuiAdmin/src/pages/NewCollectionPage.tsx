import Box from "@mui/material/Box";
import Collapse from "@mui/material/Collapse";
import Grid from "@mui/material/Grid";
import List from "@mui/material/List";
import ListItemButton from "@mui/material/ListItemButton";
import ListItemText from "@mui/material/ListItemText";
import Typography from "@mui/material/Typography";

import NewCollectionForm from "../components/forms/NewCollectionForm";
import HeaderMain from "../components/header/main";
import HeaderPage from "../components/header/page";
import Back from "../assets/back.svg";
//import { ExpandLess } from "@mui/icons-material";
import { useNavigate } from "react-router-dom";
import imagePath from "../assets/imagePath";
const NewCollectionsPage = () => {
  const navigate = useNavigate();
  return (
    <>
      <HeaderMain />
      <HeaderPage
        left={
          <Grid container alignItems={"center"}>
            <Grid item>
              <a
                href="#"
                onClick={e => {
                  navigate("/");
                }}
              >
                <img
                  style={{
                    position: "relative",
                    top: "3px",
                  }}
                  src={imagePath(Back)}
                />
              </a>
            </Grid>
            <Grid item>
              <h1>Objednat svoz</h1>
            </Grid>
          </Grid>
        }
      />
      <Box justifyContent={"center"} display={"flex"}>
        <Grid container maxWidth={"xl"} alignContent={"center"} spacing={0} marginTop={4}>
          <Grid pr={4}>
            <List component="nav">
              <ListItemButton
                onClick={e => {
                  document.getElementById("coll_detail")?.scrollIntoView();
                }}
              >
                <ListItemText>
                  <Typography fontWeight={"bold"} color={"primary"} component="span">
                    Objednávka svozu
                  </Typography>
                  {/*<ExpandLess />*/}
                </ListItemText>
              </ListItemButton>
              <Collapse in={true}>
                <Box marginLeft={2}>
                  <ListItemButton
                    onClick={e => {
                      document.getElementById("coll_detail")?.scrollIntoView();
                    }}
                  >
                    <Typography color={"primary"} component="span">
                      Podrobnosti svozu
                    </Typography>
                  </ListItemButton>
                  <ListItemButton
                    onClick={e => {
                      document.getElementById("coll_contact")?.scrollIntoView();
                    }}
                  >
                    <Typography color={"primary"} component="span">
                      Kontaktní údaje
                    </Typography>
                  </ListItemButton>
                </Box>
              </Collapse>
            </List>
          </Grid>
          <Grid>
            <NewCollectionForm
              onCreate={() => {
                navigate("/");
              }}
            />
          </Grid>
        </Grid>
      </Box>
    </>
  );
};

export default NewCollectionsPage;
