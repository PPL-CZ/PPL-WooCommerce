import { makeStyles } from "tss-react/mui";

export const useHeaderStyle = makeStyles()(theme => {
  return {
    appBar: {
      backgroundColor: "white",
      position: "static",
      paddingTop: "8px",
      paddingBottom: "8px",
      borderWidth: "0",
      boxShadow: "none",
    },
    button: {
      [`& p`]: { fontWeight: "bold" },
      ["&:hover"]: { backgroundColor: "transparent" },
      [`&.selected p`]: {
        borderBottom: "3px solid gray",
      },
      [`&.available:hover:not(.selected) p:after`]: {
        transform: "scaleX(1);",
      },
      [`&.available p:after`]: {
        display: "block",
        content: '" "',
        borderBottom: "3px solid gray",
        transform: "scaleX(0)",
        transition: "transform 250ms ease-in-out",
      },
    },
    pageBar: {
      color: theme.palette.primary.contrastText,
      backgroundColor: theme.palette.primary.main,
      height: "142px",
      [`& .MuiButton-root`]: {
        backgroundColor: theme.palette.primary.contrastText,
        color: theme.palette.primary.main,
      },
    },
  };
});
