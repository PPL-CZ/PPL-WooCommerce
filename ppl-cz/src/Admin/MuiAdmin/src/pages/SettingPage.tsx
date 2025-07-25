import Box from "@mui/material/Box";
import Collapse from "@mui/material/Collapse";
import Grid from "@mui/material/Grid";
import List from "@mui/material/List";
import ListItemButton from "@mui/material/ListItemButton";
import Typography from "@mui/material/Typography";

import HeaderMain from "../components/header/main";
import MyApi from "../components/forms/MyApiForm";
import HeaderPage from "../components/header/page";
import balik from "../assets/balik.svg";
import ShipmentPhaseForm from "../components/forms/ShipmentPhaseForm";
import SenderAddressesForm from "../components/forms/SenderAddressesForm";
import imagePath from "../assets/imagePath";
import ArrowForwardIcon from "@mui/icons-material/ArrowForward";
import ParcelPlaces from "../components/forms/ParcelPlacesForm";

const SettingPage = () => {
  return (
    <>
      <HeaderMain />
      <HeaderPage
        left={<h1>Nastavení</h1>}
        right={
          <Box display={"flex"} alignContent={"center"}>
            <Box marginRight={2}>
              <img alt={'obrázek balíčku'} src={imagePath(balik)} />
            </Box>
            <Box alignSelf={"center"}>
              <Typography margin={0} variant={"h3"}>
                Zjistěte více o možnostech PPL pluginu
              </Typography>
              <a
                href="#"
                style={{
                  color: "white",
                  textDecoration: "none",
                }}
              >
                Chci vědět více&nbsp;&nbsp;&nbsp;
                <ArrowForwardIcon
                  style={{
                    position: "relative",
                    top: "6px",
                  }}
                />
              </a>
            </Box>
          </Box>
        }
      />
      <Box justifyContent="center" display={"flex"}>
        <Grid container maxWidth={"xl"} alignContent={"right"} spacing={0} marginTop={4}>
          <Grid item xs>
            <List component="nav">
              <ListItemButton
                onClick={event => {
                    const doc = event.currentTarget.getRootNode();
                    if (doc instanceof ShadowRoot) {
                        doc.getElementById("api")?.scrollIntoView();
                    }
                }}
              >
                <Typography fontWeight={"bold"} color={"primary"}>
                  Přístupové údaje
                </Typography>
              </ListItemButton>
              <ListItemButton
                  onClick={event => {
                      const doc = event.currentTarget.getRootNode();
                      if (doc instanceof ShadowRoot) {
                          doc.getElementById("parcelplaces")?.scrollIntoView();
                      }
                  }}
              >
                <Typography fontWeight={"bold"} color={"primary"}>
                  Blokovaná výdejní místa
                </Typography>
              </ListItemButton>
              <ListItemButton
                onClick={event => {
                    const doc = event.currentTarget.getRootNode();
                    if (doc instanceof ShadowRoot) {
                        doc.getElementById("etiquete")?.scrollIntoView();
                    }
                }}
              >
                <Box display={"flex"} alignContent={"center"}>
                  <Box alignSelf={"end"} justifySelf={"auto"}>
                    <Typography fontWeight={"bold"} color={"primary"} component="span">
                      Etiketa
                    </Typography>
                  </Box>
                </Box>
              </ListItemButton>
              <Collapse in={true}>
                <Box marginLeft={2}>
                  <ListItemButton
                    onClick={event => {
                        const doc = event.currentTarget.getRootNode();
                        if (doc instanceof ShadowRoot) {
                            doc.getElementById("etiquete")?.scrollIntoView();
                        }
                    }}
                  >
                    <Typography color={"primary"} component="span">
                      Odesilatel
                    </Typography>
                  </ListItemButton>

                  <ListItemButton
                    onClick={event => {
                        const doc = event.currentTarget.getRootNode();
                        if (doc instanceof ShadowRoot) {
                            doc.getElementById("print")?.scrollIntoView();
                        }
                    }}
                  >
                    <Typography color={"primary"} component="span">
                      Tisk
                    </Typography>
                  </ListItemButton>
                </Box>
              </Collapse>
              <ListItemButton
                onClick={event => {
                    const doc = event.currentTarget.getRootNode();
                    if (doc instanceof ShadowRoot) {
                        doc.getElementById("sync")?.scrollIntoView();
                    }
                }}
              >
                <Typography fontWeight={"bold"} color={"primary"}>
                  Synchronizace
                </Typography>
              </ListItemButton>
            </List>
          </Grid>
          <Grid item xs={9}>
            <Box marginTop={2} marginBottom={2}>
              <MyApi />
            </Box>
            <Box marginTop={2} marginBottom={2}>
              <ParcelPlaces />
            </Box>
            <Box marginTop={2} marginBottom={2}>
              <SenderAddressesForm />
            </Box>
            <Box marginTop={2} marginBottom={2}>
              <ShipmentPhaseForm />
            </Box>
          </Grid>
        </Grid>
      </Box>
    </>
  );
};

export default SettingPage;
