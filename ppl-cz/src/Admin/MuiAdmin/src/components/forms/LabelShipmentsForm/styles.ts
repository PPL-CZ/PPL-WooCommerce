import { makeStyles } from "tss-react/mui";

export const useTableStyle = makeStyles()(theme => {
  return {
    hover: {
      "tr:hover": {
        backgroundColor: theme.palette.grey[100],
      },
    },
    hoverSelected: {
      " > td": {
        borderTop: "2px solid blue",
        backgroundColor: "rgb(240,240,240)",
      },
    },
    dragUsed: {
      opacity: 0.7,
    },
    trError: {
      "& td": {
        backgroundColor: theme.palette.error.light,
      },
    },
    draggable: {
      display: "inline-block",
      marginLeft: "1em",
      marginRight: "1em",
      "&:hover": {
        cursor: "pointer",
      },
    },
    ellipsis: {
      maxWidth: '20em',
      whiteSpace: "nowrap",
      overflow: 'hidden',
      textOverflow: 'ellipsis',
      display: 'block'

    }
  };
});
