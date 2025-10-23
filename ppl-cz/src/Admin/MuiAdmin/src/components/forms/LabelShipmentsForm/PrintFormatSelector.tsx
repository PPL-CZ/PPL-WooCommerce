import Button from "@mui/material/Button";
import FormLabel from "@mui/material/FormLabel";
import Grid from "@mui/material/Grid";
import { Controller, useFormContext } from "react-hook-form";
import SelectPrint from "../Inputs/SelectPrint";
import { components } from "../../../schema";
import { CreteLabelShipmentItems } from "./types";

type LabelPrintModel = components["schemas"]["LabelPrintModel"];

interface PrintFormatSelectorProps {
  printUrl: URL | null;
  availablePrinters: LabelPrintModel[];
  isCreating: boolean;
  errorMessage: string;
  onCreate: () => void;
}

export const PrintFormatSelector = ({
  printUrl,
  availablePrinters,
  isCreating,
  errorMessage,
  onCreate,
}: PrintFormatSelectorProps) => {
  const { control } = useFormContext<CreteLabelShipmentItems>();

  return (
    <Grid
      container
      spacing={2}
      p={2}
      sx={{
        justifyContent: "center",
        alignItems: "center",
      }}
    >
      <Grid item xs={5}>
        <center>
          <Controller
            key={JSON.stringify(availablePrinters)}
            control={control}
            name="labelPrintSetting"
            render={({ field: { value, onChange } }) => {
              return (
                <>
                  <Grid container>
                    <Grid item flexGrow={1}>
                      <FormLabel>Formát tisku</FormLabel>
                      <SelectPrint
                        key={JSON.stringify(availablePrinters)}
                        onChange={e => {
                          onChange(e);
                        }}
                        value={value}
                        optionals={availablePrinters || []}
                      />
                    </Grid>
                  </Grid>
                </>
              );
            }}
          />
          {printUrl ? (
            <Button
              onClick={e => {
                window.open(printUrl, "_blank");
              }}
            >
              Stáhnout štítky
            </Button>
          ) : (
            <Button
              disabled={isCreating}
              onClick={e => {
                e.preventDefault();
                onCreate();
              }}
            >
              {errorMessage ? "Zkusit znovu" : "Vytisknout zásilky"}
            </Button>
          )}
        </center>
      </Grid>
    </Grid>
  );
};
