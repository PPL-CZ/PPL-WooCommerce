import { baseConnectionUrl } from "../../connection";
import FormLabel from "@mui/material/FormLabel";
import Grid from "@mui/material/Grid";
import Skeleton from "@mui/material/Skeleton";
import { useEffect, useState } from "react";
import SelectInput from "./Inputs/SelectInput";
import { useLabelPrintSettingQuery } from "../../queries/settings";
import { useQueryLabelPrint } from "../../queries/codelists";
import imagePath from "../../assets/imagePath";
import A4List from "../../assets/list.png";
import Box from "@mui/material/Box";
import SelectPrint from "./Inputs/SelectPrint";

const SettingPrintForm = () => {
  const { isLoading, data } = useLabelPrintSettingQuery();

  const { isLoading: isLoading2, data: variants } = useQueryLabelPrint();

  const [format, setFormat] = useState(() => {
    return data || "1/PDF";
  });

  useEffect(() => {
    if (data) setFormat(data);
  }, [data]);

  if (isLoading || isLoading2) {
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
                {/*
                <SelectInput
                optionals={(variants ?? []).map(x => ({
                    label: x.title,
                    id: x.code,
                }))}
                disableClearable
                onChange={e => {
                    setFormat(e!);
                    const conn = baseConnectionUrl();
                    fetch(`${conn.url}/woocommerce-ppl/v1/setting/print`, {
                        method: "POST",
                        headers: {
                            "X-WP-Nonce": conn.nonce,
                            "content-type": "application/json",
                        },
                        body: JSON.stringify(e),
                    });
                }}
                value={format}
            />
                {format.match(/4(.[2-4])?\/PDF/) ?
                <Box pt={1} pb={1}>
                    <img src={imagePath(A4List)}/>
                </Box> : null}
                */}
            </>

        )}
      </Grid>
    </Grid>
  );
};

export default SettingPrintForm;
