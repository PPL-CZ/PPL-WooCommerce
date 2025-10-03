import SelectPrint from "../forms/Inputs/SelectPrint";
import ModalO from "../Modal";
import Button from "@mui/material/Button";
import Box from "@mui/material/Box";

const SelectPrintWidget = (props: {
    onChange: (value?: string) => void,
    value: string,
    onFinish?: () => void
    optionals: Array<{
        title: string,
        code: string
    }>
}) => {
    return <ModalO>
        <Box p={2}>
            <SelectPrint onChange={props.onChange} value={props.value} optionals={props.optionals}/>
        </Box>
        <Box p={2}>
            <Button
                onClick={e => {
                    e.preventDefault();
                    props.onFinish?.();
                }}
            >
                Zavřít
            </Button>
        </Box>
    </ModalO>
}

export default SelectPrintWidget;