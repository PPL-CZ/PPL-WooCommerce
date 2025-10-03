import Backdrop from "@mui/material/Backdrop";
import CircularProgress from "@mui/material/CircularProgress";
import { makeStyles } from "tss-react/mui";

const useBackdropStyle = makeStyles()(theme => {
  return {
    backdrop: {
      color: theme.palette.common.black,
      zIndex: 15000,
    },
  };
});

const SavingProgress = () => {
  const { classes } = useBackdropStyle();
  return (
    <Backdrop className={classes.backdrop} open>
      <CircularProgress color="inherit" />
    </Backdrop>
  );
};

export default SavingProgress;
