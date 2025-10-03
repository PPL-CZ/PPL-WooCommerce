import { makeStyles } from "tss-react/mui";

export const useImagePositionStyle = makeStyles()(theme => {
    return {
        container: {
            position: "relative",
            display: "inline-block",
            cursor: "crosshair"
        },
        image: {
            display: "block",
        },
        mask: {
            position: "absolute",
            top: 0,
            left: 0,
            width: "100%",
            height: "100%",
            background: "rgba(0, 0, 0, 0.2)", /* Tmavý překryv */
            clipPath: "inset(0 0 0 0)", /* Nastaví čtvercovou masku */
            /* Zabrání interakci s maskou */
        }
    };
});
