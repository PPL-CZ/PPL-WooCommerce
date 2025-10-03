import { makeStyles } from "tss-react/mui";
import { Button, darken } from "@mui/material";

const useHeaderButtonStyle = makeStyles()(theme => {
  return {
    headerButton: {
      padding: "1em",
      paddingRight: "1.5em",
      paddingLeft: "1.5em",
      "&:hover": {
        backgroundColor: darken(theme.palette.primary.contrastText, 0.12),
      },
    },
  };
});

const HeaderButton = (props: { onClick: () => void; children: React.ReactNode }) => {
  const { classes } = useHeaderButtonStyle();

  return (
    <Button
      onClick={e => {
        e.preventDefault();
        props.onClick();
      }}
      className={classes.headerButton}
    >
      {props.children}
    </Button>
  );
};

export default HeaderButton;
