import { baseConnectionUrl } from "../../connection";
import FormLabel from "@mui/material/FormLabel";
import Grid from "@mui/material/Grid";
import Skeleton from "@mui/material/Skeleton";
import { useEffect, useState } from "react";
import { useLabelPrintSettingQuery, usePrintOrderStatesSettingQuery} from "../../queries/settings";
import { useOrderStatuses, useQueryLabelPrint } from "../../queries/codelists";
import SelectPrint from "./Inputs/SelectPrint";
import FormControlLabel from "@mui/material/FormControlLabel";
import Checkbox from "@mui/material/Checkbox";
import Typography from "@mui/material/Typography";

const SettingPrintForm = () => {
  const { isLoading, data } = useLabelPrintSettingQuery();

  const { isLoading: isLoading2, data: variants } = useQueryLabelPrint();

  const { isLoading: isLoading4, data: _printOrderStatuses } = usePrintOrderStatesSettingQuery();

  const { isLoading: isLoading3, data: statuses } = useOrderStatuses();

  const [printOrderStatuses, setPrintOrderStatuses] = useState(_printOrderStatuses);

  useEffect(() => {
    setPrintOrderStatuses(_printOrderStatuses);
  }, [_printOrderStatuses]);


  const [format, setFormat] = useState(() => {
    return data || "1/PDF";
  });

  useEffect(() => {
    if (data) setFormat(data);
  }, [data]);

  if (isLoading || isLoading2 || isLoading3 || isLoading4) {
    return <Skeleton height={150} sx={{ transform: "scale(1,1)" }} />;
  }

  return (
    <Grid id="print" container alignItems={"center"}>
      <Grid item xs={4}>
        <FormLabel>Tiskový formát štítku</FormLabel>
      </Grid>
      <Grid item xs={4}>
        {isLoading ? (
          <Skeleton />
        ) : (
            <>
                <SelectPrint optionals={variants ?? []}
                             value={format}
                             name={'print'}
                             onChange={e => {
                                 setFormat(e!);
                                 const conn = baseConnectionUrl();
                                 fetch(`${conn.url}/ppl-cz/v1/setting/print`, {
                                     method: "POST",
                                     headers: {
                                         "X-WP-Nonce": conn.nonce,
                                         "content-type": "application/json",
                                     },
                                     body: JSON.stringify(e),
                                 });
                             }}/>
                <Typography variant="h4" mt={2}>
                    Jaké stavy přidat do filtru pro tisk objednávek
                </Typography>
                {(statuses || []).map(item => (
                    <><FormControlLabel
                        control={
                            <Checkbox
                                id={item.code}
                                name={`${item.code}`}
                                checked={(printOrderStatuses?.indexOf(item.code) ?? -1) > -1}
                                onChange={e => {
                                    let newPrintOrderStatuses = printOrderStatuses || [];

                                    if (newPrintOrderStatuses.indexOf(item.code) > -1)
                                        newPrintOrderStatuses = newPrintOrderStatuses.filter(x => x !== item.code);
                                    else
                                    {
                                        newPrintOrderStatuses = newPrintOrderStatuses.concat([item.code]);
                                    }

                                    setPrintOrderStatuses(newPrintOrderStatuses);

                                    const conn = baseConnectionUrl();
                                    fetch(`${conn.url}/ppl-cz/v1/setting/print-order-statuses`, {
                                        method: "POST",
                                        headers: {
                                            "X-WP-Nonce": conn.nonce,
                                            "content-type": "application/json",
                                        },
                                        body: JSON.stringify(newPrintOrderStatuses),
                                    });

                                }}
                            />
                        }
                        label={item.title}
                    /><br/></>
                ))}
            </>
        )}
      </Grid>
    </Grid>
  );
};

export default SettingPrintForm;
