import Box from "@mui/material/Box";
import Typography from "@mui/material/Typography";
import Snackbar from "@mui/material/Snackbar";
import HeaderMain from "../components/header/main";
import HeaderPage from "../components/header/page";
import { useNavigate } from "react-router-dom";
import Email from "@mui/icons-material/Email";
import Copy from "@mui/icons-material/ContentCopy";
import TextField from '@mui/material/TextField';
import HeaderButton from "../components/HeaderButton";
import useHeaderStyle from "./useHeaderStyle";
import {useLogs, sendLog, deleteLog} from "../queries/useLogsQueries";
import Markdown from "react-markdown";
import {makeStyles} from "tss-react/mui";
import { components } from "../schema";
import {Fragment, useEffect, useState} from "react";
import FormLabel from "@mui/material/FormLabel";
import useMarkdownStyle from "./useMarkdownStyle";

type ErrorLogItemModel = components['schemas']['ErrorLogItemModel'];
type SendErrorLogModel = components["schemas"]["SendErrorLogModel"];


const LogPage = () => {

    const navigate = useNavigate();
    const { classes } = useHeaderStyle();
    const { classes: markdownClasses} = useMarkdownStyle();

    const { data, isLoading } = useLogs();

    const [errors, setErrors ] = useState<ErrorLogItemModel[]>([])
    const [note, setNote] = useState("");
    const [message, setMessage] = useState("");
    const [mail, setMail] = useState("");
    const [sending, setSending] = useState(false);

    const copy = ()=> {
        navigator.clipboard.writeText(
            "Kontakt: " + mail + "\n\nPopis problému: " + note + "\n\n" + data?.info + "\n\n###\nVýpis systémových logů:\n\n" + (errors.map(x => "###\nChyba\n" + x.trace + "\n")).join("\n")
        ).then(x => {
            setMessage("Zkopírováno do schránky")
        });
    }

    let temporarySending = false;
    const send =  (ev: any = null) => {
        if (ev)
            ev.preventDefault();

        if (sending || temporarySending)
            return;

        temporarySending = true;

        const sendData: SendErrorLogModel = {
            mail,
            message: note,
            info: data?.info,
            errors: errors
        }
        setSending(true);
        sendLog(sendData).then(x => {
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
        const removeError = errors[index];
        if (removeError) {
            setErrors(errors.filter((x, cindex) => x.id !== removeError.id));
            deleteLog(removeError);
        }
    }


    useEffect(() => {
        setErrors(data?.errors ?? []);
        setMail(data?.mail || "");
    }, [data]);

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
                                <Email style={{ color: "gray" }} />
                                &nbsp;<strong>Odeslat</strong>
                            </HeaderButton>
                            <HeaderButton
                                onClick={copy}
                            >
                                <Copy  style={{ color: "gray" }} />
                                &nbsp;<strong>Do schránky</strong>
                            </HeaderButton>
                        </Box>
                    </Box>
                }
            />
            <Box p={2} className={markdownClasses.markdown}>
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
            <Box p={2} className={markdownClasses.markdown}>
                {data ? <Markdown>{data.info}</Markdown> : <Markdown>Stahuji logy</Markdown>}
                {errors.length ?  <><h3>Výpis systémových logů</h3>
                    {errors.map(((x,index) => <Fragment key={index}>
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
