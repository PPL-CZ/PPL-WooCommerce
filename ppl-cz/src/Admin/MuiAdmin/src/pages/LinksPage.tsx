import Box from "@mui/material/Box";
import HeaderMain from "../components/header/main";
import HeaderPage from "../components/header/page";

import balik from "../assets/balik.svg";
import facebook from "../assets/facebook.svg";
import github from "../assets/github.svg";
import dokument from "../assets/dokument.svg";

import imagePath from "../assets/imagePath";
import useMarkdownStyle from "./useMarkdownStyle";
import Card from "@mui/material/Card";

const LinksPage = () => {

    const { classes: markdownClasses} = useMarkdownStyle();
    return (
        <>
            <HeaderMain />
            <HeaderPage
                left={<h1>Odkazy</h1>}
                right={
                    <Box display={"flex"} alignContent={"center"}>
                        <Box marginRight={2}>
                            <img alt={'obrázek balíčku'} src={imagePath(balik)} />
                        </Box>
                    </Box>
                }
            />
            <Box justifyContent="center">

                <Card >
                    <Box m={4} maxWidth={'700px'} className={markdownClasses.markdown}>
                        <ul>
                            <li className={'none'}>
                                <img alt={'dokumentace'} src={imagePath(dokument)} /> <a href={'https://ppl-plugin-woo.apidog.io/'} target={"_blank"}>Uživatelská dokumentace - PPL WooCommerce plugin</a>
                            </li>
                            <li className={'none'}>
                                <img alt={'facebook.com'} src={imagePath(facebook)} /> <a href={'https://www.facebook.com/pplcz/' } target={"_blank"}>Facebook</a>
                            </li>
                            <li  className={'none'}>
                                <img alt={'github.com'} src={imagePath(github)} /> <a href={'https://github.com/PPL-CZ/PPL-WooCommerce'} target={"_blank"}>Github</a>
                            </li>
                        </ul>
                    </Box>
                </Card>
            </Box>
        </>
    );
};

export default LinksPage;
