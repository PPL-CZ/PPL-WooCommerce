import Alert from "@mui/material/Alert";
import {AlertProps} from "@mui/material/Alert";
import {useEffect, useState} from "react";


export const SaveInfo = (props: AlertProps & { timeout: number}) => {
    const [wait, setWait] = useState(props.timeout > 0);

    useEffect(() => {
        const ref = setTimeout(()=> {
            setWait(false);
        }, props.timeout);
        return () => clearTimeout(ref);
    }, []);

    if (wait)
        return <Alert {...props} />
    return null;
}

export default SaveInfo;