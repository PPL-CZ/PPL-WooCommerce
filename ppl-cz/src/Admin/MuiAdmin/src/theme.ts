import { createTheme } from "@mui/material/styles";
import themeSetting from "./theme.setting";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
const theme = createTheme(themeSetting as any);
export default theme;




export const shadowTheme = (shadowRoot: ShadowRoot) => {
    const newTheme = JSON.parse(JSON.stringify(themeSetting));

    newTheme.components.MuiPopover.defaultProps.container = shadowRoot;

    newTheme.components.MuiModal = {
        defaultProps: {
            container: shadowRoot
        }
    };

    return createTheme(newTheme);
}