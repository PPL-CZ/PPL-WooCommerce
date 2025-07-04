import Box from "@mui/material/Box";
import HeaderMain from "../components/header/main";
import HeaderPage from "../components/header/page";
import balik from "../assets/balik.svg";
import imagePath from "../assets/imagePath";
import useMarkdownStyle from "./useMarkdownStyle";
import Card from "@mui/material/Card";

const NewsPage = () => {



    const { classes: markdownClasses} = useMarkdownStyle();
    return (
        <>
            <HeaderMain />
            <HeaderPage
                left={<h1>Novinky</h1>}
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
                        <li>
                        Zákazník si nyní může v rámci konfigurace nastavit, která výdejní místa nechce zobrazovat ve výdejním widgetu. Tato místa budou z widgetu automaticky vyfiltrována a nebudou nabízená koncovým uživatelům při výběru doručení.
                        </li>
                        <li>
                        Do konfigurace byla přidána možnost definovat, které země a jazyky se nemají zobrazovat ve výdejním widgetu. Zákazníci tak mohou snadno omezit nabídku lokalizací podle svých obchodních potřeb.
                        </li>
                        <li>
                        Byla přidána možnost definovat cenu dopravy na základě váhových pásem objednaného zboží. V konfiguraci lze nastavit konkrétní cenové hladiny – např. do 10 kg za 100 Kč, do 20 kg za 200 Kč atd. Výsledná cena dopravy se poté automaticky vypočítá podle celkové váhy košíku.
                        </li>
                            <li>
                        Opraveno neukládání formátu etikety vedoucí k chybnému tisku.
                            </li>
                        <li>
                        Opraven problém, kdy nebylo možné objednat svoz téhož dne, pokud ještě nebylo 9:00
                        </li>
                        </ul>
                    </Box>
                </Card>
            </Box>
        </>
    );
};

export default NewsPage;
