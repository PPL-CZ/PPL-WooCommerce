import { makeStyles } from "tss-react/mui";

export const useTableStyle = makeStyles()(theme => {
  return {
    trError: {
      "& td": {
        backgroundColor: theme.palette.error.light,
      },
    },
  };
});
