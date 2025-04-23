import { ThemeProvider } from "@mui/material/styles";
import createCache from "@emotion/cache";
import Theme, {shadowTheme} from "../../theme";
import {useMemo} from "react";
import {TssCacheProvider} from "tss-react";



const ThemeContextOverlay = (props: { children: React.ReactNode, shadowContainer?: ShadowRoot }) => {
    const cssCache = useMemo(() => {
        if (props.shadowContainer) {
            return createCache({
                key: "css",
                prepend: true,
                container: props.shadowContainer
            })
        }
        return  null;
    }, []);

    const theme = useMemo(() => {
        if (props.shadowContainer) {
            return shadowTheme(props.shadowContainer);
        }

        return Theme;

    }, []);

    if (cssCache)
        return <TssCacheProvider value={cssCache}>
            <ThemeProvider theme={theme}>{props.children}</ThemeProvider>
        </TssCacheProvider>;
    return <ThemeProvider theme={theme}>{props.children}</ThemeProvider>
};

export default ThemeContextOverlay;
