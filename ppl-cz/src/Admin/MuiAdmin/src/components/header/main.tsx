import AppBar from "@mui/material/AppBar";
import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import IconButton from "@mui/material/IconButton";
import Menu from "@mui/material/Menu";
import MenuItem from "@mui/material/MenuItem";
import Toolbar from "@mui/material/Toolbar";
import Typography from "@mui/material/Typography";
import MenuIcon from "@mui/icons-material/Menu";

import logo from "../../assets/logo.svg";
import { useState } from "react";
import { useHeaderStyle } from "./styles";
import { useLocation, useNavigate } from "react-router-dom";
import imagePath from "../../assets/imagePath";
const pages = [
  { key: "/", label: "Svozy" },
  {
    key: "/setting",
    label: "Nastaveni",
  },
  { key: "/news", label: "Novinky"},
  { key: "/links", label: "Odkazy" },
  { key: "/logs", label: "Nahlásit problém"}
];

const HeaderMain = () => {
  const navigation = useNavigate();
  const location = useLocation();

  const { classes } = useHeaderStyle();

  const [anchorElNav, setAnchorElNav] = useState<null | HTMLElement>(null);

  const handleOpenNavMenu = (event: React.MouseEvent<HTMLElement>) => {
    setAnchorElNav(event.currentTarget);
  };

  const handleCloseNavMenu = (item?: string) => {
    if (item) navigation(`${item}`);
    else navigation("/");
  };

  return (
    <AppBar component={"div"} className={classes.appBar}>
      <Toolbar>
        <Typography
          variant="h6"
          noWrap
          component="a"
          href="#app-bar-with-responsive-menu"
          sx={{
            mr: 10,
            display: { xs: "none", md: "flex" },
          }}
        >
          <img alt={"logo"} src={imagePath(logo)} />
        </Typography>

        <Box sx={{ flexGrow: 1, display: { xs: "flex", md: "none" } }}>
          <IconButton
            size="large"
            aria-label="account of current user"
            aria-controls="menu-appbar"
            aria-haspopup="true"
            onClick={handleOpenNavMenu}
            color="primary"
          >
            <MenuIcon />
          </IconButton>
          <Menu
            id="menu-appbar"
            anchorEl={anchorElNav}
            anchorOrigin={{
              vertical: "bottom",
              horizontal: "left",
            }}
            keepMounted
            transformOrigin={{
              vertical: "top",
              horizontal: "left",
            }}
            open={Boolean(anchorElNav)}
            onClose={e => {
              handleCloseNavMenu();
            }}
            sx={{
              display: { xs: "block", md: "none" },
            }}
          >
            {pages.map((page, index) => (
              <MenuItem
                key={index}
                onClick={e => {
                  handleCloseNavMenu(page.key);
                }}
              >
                <Typography textAlign="center">{page.label}</Typography>
              </MenuItem>
            ))}
          </Menu>
        </Box>
        <Typography
          variant="h6"
          noWrap
          component="a"
          href="#app-bar-with-responsive-menu"
          sx={{
            display: { xs: "flex", md: "none" },
            alignSelf: "end",
          }}
        >
          <img alt="logo" src={imagePath(logo)} />
        </Typography>

        <Box sx={{ display: { xs: "none", md: "flex", gap: "2em", alignSelf: "center" } }}>
          {pages.map((page, index) => {
            const has = location.pathname || "/";
            let selected = "";
            if ((has === "/" && page.key === "/") || (has !== "/" && page.key.startsWith(has))) {
              selected = "selected";
            }

            return (
              <Button
                key={index}
                className={`${classes.button} available ${selected}`}
                variant="text"
                onClick={e => {
                  handleCloseNavMenu(page.key);
                }}
              >
                <Typography>{page.label}</Typography>
              </Button>
            );
          })}
        </Box>
      </Toolbar>
    </AppBar>
  );
};

export default HeaderMain;
