import Box from "@mui/material/Box";
import Modal from "@mui/material/Modal";
import Dialog from "@mui/material/Dialog";
import React, { useEffect, useState } from "react";
import { makeStyles } from "tss-react/mui";

export const useHeaderStyle = makeStyles()(theme => {
  return {
    children: {
      "& .modalBox": {
        maxHeight: "60vh",
        overflow: "auto",
      },
    },
    mainModal: {
      position: "absolute",
      top: "50%",
      left: "50%",
      transform: "translate(-50%, -50%)",
      width: "800px",
      maxHeight: "90vh",
      maxWidth: "90vw",
      backgroundColor: theme.palette.background.paper,
      boxShadow: "24",
      zIndex: 15000,
    },
  };
});

const ModalO = (props: { children?: React.ReactNode; onClose?: (reason: any) => void; width?: "min" | "max" }) => {
  const [leftMargin, setLeftMargin] = useState(0);

  useEffect(() => {
    if ((props?.width ?? "min") === "min") return;

    const onResize = () => {
      const wrapper = document.getElementById("adminmenuwrap");
      const width = wrapper?.getBoundingClientRect().width ?? 0;
      setLeftMargin(width);
    };

    window.addEventListener("resize", onResize);
    onResize();
    return () => window.removeEventListener("resize", onResize);
  }, [props.width]);

  const { classes, cx } = useHeaderStyle();

  const styles = (() => {
    if ((props?.width ?? "min") === "min") return {};

    const lm2 = leftMargin / 2;
    return {
      transform: `translate(calc(-50% + ${lm2}px), -50%)`,
      width: `calc(90vw - ${leftMargin}px)`,
      maxWidth: "1024px",
    };
  })();

  return (
    <Modal open={true} onClose={(ev, reason) => props.onClose?.(reason)} className={`wp-reset-div`}>
      <Box className={cx(classes.mainModal, classes.children)} style={styles}>
        {props.children}
      </Box>
    </Modal>
  );
};

export default ModalO;
