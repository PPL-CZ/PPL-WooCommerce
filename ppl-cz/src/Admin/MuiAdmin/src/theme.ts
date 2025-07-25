import { createTheme } from "@mui/material/styles";
import themeSetting from "./theme.setting";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
const theme = createTheme(themeSetting as any);
export default theme;




export const shadowTheme = (shadowRoot: HTMLElement) => {
    const newTheme = JSON.parse(JSON.stringify(themeSetting));


    newTheme.components ||= {}

    newTheme.components.MuiPopover ||= {}
    newTheme.components.MuiPopover.defaultProps ||= {}
    newTheme.components.MuiPopover.defaultProps.container = shadowRoot;

    newTheme.components.MuiPopper ||= {}
    newTheme.components.MuiPopper.defaultProps ||= {}
    newTheme.components.MuiPopper.defaultProps.container = shadowRoot;

    newTheme.components.MuiModal ||= {}
    newTheme.components.MuiModal.defaultProps ||= {}
    newTheme.components.MuiModal.defaultProps.container = shadowRoot;

    return createTheme(newTheme);
}