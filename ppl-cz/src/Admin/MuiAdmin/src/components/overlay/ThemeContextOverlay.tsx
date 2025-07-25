import { ThemeProvider } from "@mui/material/styles";
import createCache from "@emotion/cache";
import Theme, {shadowTheme} from "../../theme";
import {useMemo} from "react";
import {TssCacheProvider} from "tss-react";
import {CacheProvider} from "@emotion/react";



const ThemeContextOverlay = (props: { children: React.ReactNode, shadowContainer?: ShadowRoot, shadowRootElement?: HTMLElement }) => {
    const cssCache = useMemo(() => {
        if (props.shadowContainer) {
            return createCache({
                key: "css",
                prepend: true,
                container: props.shadowContainer
            })
        }
        return  null;
    }, [props.shadowContainer]);

    const theme = useMemo(() => {
        if (props.shadowRootElement) {
            return shadowTheme(props.shadowRootElement);
        }

        return Theme;

    }, [props.shadowRootElement]);

    if (cssCache)
        return <CacheProvider value={cssCache}>
            <ThemeProvider theme={theme}>{props.children}</ThemeProvider>
        </CacheProvider>;
    return <ThemeProvider theme={theme}>{props.children}</ThemeProvider>
};

export default ThemeContextOverlay;
