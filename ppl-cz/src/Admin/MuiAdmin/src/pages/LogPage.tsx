import Box from "@mui/material/Box";
import Typography from "@mui/material/Typography";
import Snackbar from "@mui/material/Snackbar";
import HeaderMain from "../components/header/main";
import HeaderPage from "../components/header/page";
import { useSearchParams} from "react-router-dom";
import Email from "@mui/icons-material/Email";
import Copy from "@mui/icons-material/ContentCopy";
import TextField from '@mui/material/TextField';
import HeaderButton from "../components/HeaderButton";
import useHeaderStyle from "./useHeaderStyle";
import {useLogs, sendLog, deleteLog} from "../queries/useLogsQueries";
import Markdown from "react-markdown";
import { components } from "../schema";
import {Fragment, useEffect, useState} from "react";
import FormLabel from "@mui/material/FormLabel";
import useMarkdownStyle from "./useMarkdownStyle";
import Autocomplete from "@mui/material/Autocomplete";
import CircularProgress from "@mui/material/CircularProgress";

import  jszip from "jszip"

type ErrorLogItemModel = components['schemas']['ErrorLogItemModel'];
type SendErrorLogModel = components["schemas"]["SendErrorLogModel"];
type ErrorLogModel = components['schemas']['ErrorLogModel'];


const LogPage = () => {


    const { classes } = useHeaderStyle();
    const { classes: markdownClasses} = useMarkdownStyle();

    const [searchParams, setSearchParams] = useSearchParams();


    const { data, isLoading } = useLogs(
            searchParams.get('product_ids')?.split(',')?.sort() ?? [],
            searchParams.get('order_ids')?.split(',')?.sort() ?? []
    );

    const [sendData, setSendData] = useState<ErrorLogModel|undefined>(data);


    const [note, setNote] = useState("");
    const [message, setMessage] = useState("");
    const [mail, setMail] = useState("");
    const [sending, setSending] = useState(false);

    const copy = ()=> {
            const data = new jszip();
            if (sendData?.globalParcelSetting)
                data.file("globalni_nastaveni_parcel.json", JSON.stringify(sendData!.globalParcelSetting, null, 3));
            if (sendData?.productsSetting)
                data.file("nastaveni_produktu.json", JSON.stringify(sendData!.productsSetting, null, 3));
            if (sendData?.categorySetting)
                data.file("nastaveni_kategorii.json", JSON.stringify(sendData!.categorySetting, null, 3));
            if (sendData?.shipmentsSetting)
                data.file("nastaveni_dopravy.json", JSON.stringify(sendData!.shipmentsSetting, null, 3));
            if (sendData?.orders)
                data.file("objednavky.json", JSON.stringify(sendData!.orders, null, 3));

            let content = note ? ("\n\n===Poznámka===\n" + note) : "";
            if (sendData)
            {
                content += "\n\n===Info===\n" + sendData!.info;
                if (sendData.errors)
                {
                    content +=  "\n\n===Logy===\n" + sendData.errors.map(x => {
                        return x.trace
                    }).join("\n\n")
                }
            }

            data.file("zprava_a_logy.txt", content);
            data.generateAsync({ type: "blob"}).then((content) => {
                const url = URL.createObjectURL(content);

                const link = document.createElement("a");
                link.href = url;
                link.download = "pplcz_info.zip";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            })

    }

    let temporarySending = false;
    const send =  (ev: any = null) => {
        if (ev)
            ev.preventDefault();

        if (sending || temporarySending)
            return;

        temporarySending = true;

        setSending(true);
        const sendData2 = JSON.parse(JSON.stringify(sendData)) as SendErrorLogModel;

        sendData2.info = "### Poznámka\n" + note +"\n\n" + (sendData2.info || "");

        sendLog(sendData2 as SendErrorLogModel).then(x => {
            if (x === null) {
                setNote("");
                setMessage("Úspěšný pokus o odeslání");
            } else {
                setMessage("Problém s odesláním");
            }

        }).catch(() => setMessage("Problém s odesláním")).finally(() => setSending(false));
    }


    const removeItem = (index:number, ev:any) => {
        ev.preventDefault();
        const removeError = sendData?.errors?.[index];

        if (removeError) {
            if (sendData) {
                setSendData({
                    ...sendData,
                    errors: sendData.errors!.filter(x => x.id !== removeError.id)
                });
            }
            deleteLog(removeError);
        }
    }

    const removeCategories = (ev: any)=> {
        ev.preventDefault();
        const newData = {...sendData!};
        newData.categorySetting = undefined;
        setSendData(newData);
    }


    const removeGlobal = (ev: any) => {
        ev.preventDefault();
        const newData = {...sendData!};
        newData.globalParcelSetting = undefined;
        setSendData(newData);
    }

    const removeOrders = (ev: any) => {
        ev.preventDefault();
        const newData = {...sendData!};
        newData.orders = undefined;
        setSendData(newData);
    }

    const removeShipments = (ev: any)=> {

        ev.preventDefault();
        const newData = {...sendData!};
        newData.shipmentsSetting = undefined;
        setSendData(newData);
    }

    const removeProducts = (ev: any)=> {
        ev.preventDefault();
        const newData = {...sendData!};
        newData.productsSetting = undefined;
        setSendData(newData);
    }

    useEffect(() => {
        if (data) {
            setMail(data?.mail || "");
            setSendData( {
                ...data,
                productsSetting: data.productsSetting
            });
        }
    }, [data]);

    const hledaniProduktu = <Autocomplete style={{maxWidth: '600px'}} multiple value={searchParams.get("product_ids")?.split(",").filter(x=>parseInt(x)) ?? []} onChange={(event, newValue:any)=>{
        // @ts-ignore
        searchParams.set("product_ids", [...new Set(newValue.map(x => parseInt(x)))].join(','));
        setSearchParams(searchParams);
    }} options={[]} freeSolo renderInput={(params: any)=>{
        return <TextField
            {...params}
            label="Přidej produkt (id)"
            placeholder="Napiš a stiskni Enter"
        />
    }} />

    const hledaniObjednavky = <Autocomplete style={{maxWidth: '600px'}} multiple value={searchParams.get("order_ids")?.split(",").filter(x=>parseInt(x)) ?? []} onChange={(event, newValue:any)=>{
        // @ts-ignore
        searchParams.set("order_ids", [...new Set(newValue.map(x => parseInt(x)))].join(','));
        setSearchParams(searchParams);
    }} options={[]} freeSolo renderInput={(params: any)=>{
        return <TextField
            {...params}
            label="Přidej objednávku (id)"
            placeholder="Napiš a stiskni Enter"
        />
    }} />

    return (
        <>
            <HeaderMain />
            <HeaderPage
                left={
                    <Typography component={"h1"} className={classes.h1}>
                        Nahlásit problém
                    </Typography>
                }
                right={
                    <Box display={"flex"} alignContent={"center"}>
                        <Box alignSelf={"center"}>
                            <HeaderButton
                                onClick={send}
                            >
                                {sending ? <CircularProgress size={"1em"}/>: <Email style={{ color: "gray" }} />}
                                &nbsp;<strong>Odeslat</strong>
                            </HeaderButton>
                            <HeaderButton
                                onClick={copy}
                            >
                                <Copy  style={{ color: "gray" }} />
                                &nbsp;<strong>Stáhnout pro odeslání</strong>
                            </HeaderButton>
                        </Box>
                    </Box>
                }
            />
            <Box p={2} className={markdownClasses.markdown}  maxWidth={"xl"} marginLeft={'auto'} marginRight={'auto'}>
                <Box pb={2}>
                <FormLabel>Kontakt</FormLabel>
                <TextField disabled={sending} placeholder={"Kontakt"} value={mail} onChange={e => {
                    setMail(e.target.value)
                }}/></Box>
                <TextField disabled={sending} placeholder={"Sem popiště svůj problém"} value={note} onChange={e => {
                    setNote(e.target.value)
                }} multiline/>
                <hr/>
            </Box>

            <Box p={2} className={markdownClasses.markdown} maxWidth={"xl"} marginLeft={'auto'} marginRight={'auto'}>
                {sendData ? <Markdown>{sendData.info}</Markdown> : <Markdown>Stahuji logy</Markdown>}
                {sendData?.shipmentsSetting ?
                    sendData?.shipmentsSetting.length ? <>
                    <h3>Nastavení dopravy <a href={'#'} style={{ fontWeight: 'normal'}} onClick={removeShipments}>[Nereportovat]</a></h3>
                        {sendData.shipmentsSetting.map((x,index) => {
                            const data = JSON.stringify(x, null, 3)  as BlobPart;
                            const blob = new Blob([data], { type: "application/json"});
                            const url = URL.createObjectURL(blob);
                            return <Fragment key={index}>
                                <a href={url} target={"_blank"}>
                                    {x.name}
                                </a><br/>
                            </Fragment>

                        })}

                </> :
                    <div><h3>Nastavení dopravy</h3>
                        Není nastavena doprava</div> : null}

                {sendData?.globalParcelSetting ? <>
                            <h3>Globální nastavení dopravy (parcelbox) <a href={'#'} style={{ fontWeight: 'normal'}} onClick={removeGlobal}>[Nereportovat]</a></h3>
                        </> : null}

                {sendData?.categorySetting ?
                    (sendData?.categorySetting.length ? <>
                <h3>Nastavení kategorií <a href={'#'} style={{ fontWeight: 'normal'}} onClick={removeCategories}>[Nereportovat]</a></h3>{(()=>{
                    const str = JSON.stringify(sendData.categorySetting, null, 3)  as BlobPart;
                    const blob = new Blob([str], { type: "application/json"});
                    const url = URL.createObjectURL(blob);
                    return <div><a href={url} target={"_blank"}>Kategorie</a></div>
                })()}</> : <div><h3>Nastavení dopravy </h3>
                Žádna kategorie není nastena</div>) : null}


                {sendData?.productsSetting ?
                    (sendData?.productsSetting.length ? <>
                        <h3>Nastavení produktů ({sendData.productsSetting.length}) <a href={'#'} style={{ fontWeight: 'normal'}} onClick={removeProducts}>[Nereportovat]</a></h3>
                        {hledaniProduktu}{(()=>{
                        const str = JSON.stringify(sendData.productsSetting, null, 3)  as BlobPart;
                        const blob = new Blob([str], { type: "application/json"});
                        const url = URL.createObjectURL(blob);
                        return <div><a href={url} target={"_blank"}>Produkty</a>&nbsp;&nbsp;
                            {sendData?.productsSetting?.map(((x,index) => <span key={index}>{x.name},</span>))}
                            <br/>
                            Volitelné: Zadejte další ID produktu, se kterým máte problém, funkce následně zařadí její data do exportu. V případě, že zařadíte problémové objednávky, produkty v těchto objednávkach se zahrnou do zmíněných dat.
                        </div>
                    })()}</> : <div><h3>Nastavení produktů</h3>
                        {hledaniProduktu}<br/>
                        Žádný produkt není nastaven<br/>
                        Volitelné: Zadejte ID produktu, se kterým máte problém, funkce následně zařadí její data do exportu. V případě, že zařadíte problémové objednávky, produkty v těchto objednávkach se zahrnou do zmíněných dat.
                        </div>) : null}

                {sendData?.orders ?
                    (sendData?.orders.length ? <>
                        <h3>Objednávky ({sendData.orders.length}) <a href={'#'} style={{ fontWeight: 'normal'}} onClick={removeOrders}>[Nereportovat]</a></h3>
                        {hledaniObjednavky}{(()=>{
                        const str = JSON.stringify(sendData.orders, null, 3)  as BlobPart;
                        const blob = new Blob([str], { type: "application/json"});
                        const url = URL.createObjectURL(blob);
                        return <div><a href={url} target={"_blank"}>Objednávky</a>&nbsp;&nbsp;
                            {searchParams.get("order_ids")?.trim() ? sendData?.orders?.map(((x,index) => <span key={index}>{(x as unknown as any)['id']},</span>)) : null}
                        <br/> Volitelné: Zadejte ID další objednávky, se kterou máte problém, funkce následně zařadí její data do exportu (mailu).
                        </div>
                    })()}</> : <div><h3>Problémové objednávky</h3>
                        {hledaniObjednavky}<br/>
                        Žádná objednávka není nastavena<br/>
                        Volitelné: Zadejte ID objednávky, se kterou máte problém, funkce následně zařadí její data do exportu (mailu).</div>) : null}

                {sendData?.errors?.length ?  <><h3>Výpis systémových logů</h3>
                    {sendData.errors.map(((x,index) => <Fragment key={index}>
                        {x.trace?.split(/\n/g).map(((p, index1) => {
                            return <Fragment key={index1}>
                                {p}{index1 === 0 ? <>&nbsp;&nbsp;<a href={'#'} onClick={removeItem.bind(null, index1)}>[Nereportovat]</a></> : null}<br/>
                            </Fragment>
                        }))}
                        <hr/>
                    </Fragment>))}
                </> : null}
            </Box>
            <Snackbar
                anchorOrigin={{ vertical: "bottom", horizontal: "right" }}
                open={!!message}
                autoHideDuration={6000}
                onClose={() => setMessage("")}
                message={message}
                />
        </>
    );
};

export default LogPage;
