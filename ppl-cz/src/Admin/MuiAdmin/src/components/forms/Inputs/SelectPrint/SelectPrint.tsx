import SelectInput from "../SelectInput";
import {useEffect, useMemo, useState} from "react";
import ImagePosition from "./ImagePosition";
import Typography from "@mui/material/Typography"

const SelectPrint = (props: {
    onChange?: (val?: string) => void,
    value?: string,
    optionals: { title: string, code: string }[]
}) => {
    const [refreshValue, setRefreshValue] = useState(1);
    const availableValues = useMemo(() =>
        props.optionals.reduce((acc, value) => {
            const matched = value.code.match(/4\.[2-4]\/PDF/);
            acc.push({
                id: value.code,
                label: value.title
            })
            return acc;
        }, [] as Array<{id: string, label: string}>), [JSON.stringify(props.optionals)])

    const getPosition = () => {
        const matched = props.value?.match(/4\.([2-4])\/PDF/);
        return parseInt(matched?.[1] || "1");
    }

    const getValue = () => {
        return props.value;
    }

    const [ position, setPosition] = useState(getPosition);

    useEffect(() => {
        setPosition(getPosition());
    }, [props.value]);

    return <>
        <SelectInput
            key={`${JSON.stringify(availableValues)}${refreshValue}`}
            disableClearable
            onChange={e => {
                props.onChange?.(e);
            }}
            value={getValue()}
            optionals={availableValues}
        />
        {['4/PDF', '4.2/PDF', '4.3/PDF', '4.4/PDF'].indexOf(getValue() || '') > -1 ? <>
            <Typography component={"p"}>Na obrázku je zvýrazněna pozice první zásilky</Typography>
            <ImagePosition position={position} onChange={(position )=> {
                props.onChange?.(position === 1 ?`4/PDF` : `4.${position}/PDF`)
                setRefreshValue(refreshValue + 1);
            }} width={212} height={150} />
        </> : null}
    </>
}

export default SelectPrint;

