import { makeStyles } from "tss-react/mui";

const useHeaderStyle = makeStyles()(theme => {
  return {
    h1: {
      fontWeight: "700",
      fontSize: 23,
      lineHeight: 37.5,
    },
  };
});

export default useHeaderStyle;
