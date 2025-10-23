import { useFieldArray, useForm, FormProvider, Controller, useFormState } from "react-hook-form";
import { components } from "../../../schema";
import { useEffect, useMemo, useState } from "react";

import { Alert, FormHelperText } from "@mui/material";
import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import FormLabel from "@mui/material/FormLabel";
import Grid from "@mui/material/Grid";
import Switch from "@mui/material/Switch";
import Table from "@mui/material/Table";
import TableBody from "@mui/material/TableBody";
import TableCell from "@mui/material/TableCell";
import TableContainer from "@mui/material/TableContainer";
import TableHead from "@mui/material/TableHead";
import TableRow from "@mui/material/TableRow";

import ShipmentMethodController from "../Controllers/ShipmentMethodController";
import TextFieldController from "../Controllers/TextFieldController";
import { useQueryShipmentMethods } from "../../../queries/codelists";
import CurrencyController from "../Controllers/CurrencyController";
import PackageForm from "./PackageForm";
import SelectInput from "../Inputs/SelectInput";
import { useSenderAddressesQuery } from "../../../queries/settings";
import { ValidationErrorException } from "../../../queries/types";
import SavingProgress from "../../SavingProgress";
import SaveInfo from "./SaveInfo";
import { useUpdateShipmentMutation } from "../../../queries/useShipmentQueries";

type UpdateShipmentModel = components["schemas"]["UpdateShipmentModel"];
type ShipmentModel = components["schemas"]["ShipmentModel"];

const createUpdateShipmentModel = (data: ShipmentModel) => {
  if (!data) {
    return {
      referenceId: "",
      serviceCode: "",
      packages: [{}],
    };
  } else {
    return {
      referenceId: data.referenceId || "",
      serviceCode: data.serviceCode || "",
      orderId: data.orderId,
      senderId: data.sender?.id,
      hasParcel: data.hasParcel,
      parcelId: data.parcel?.id,
      note: data.note,
      age: data.age,
      packages: Array.from(data.packages || [{}]).map(x => {
        if (Array.isArray(x)) {
          return {};
        }
        return x;
      }),
      codValue: data.codValue,
      codVariableNumber: data.codVariableNumber,
      codValueCurrency: data.codValueCurrency,
    };
  }
};

const ErrorPackage = () => {
  const { errors } = useFormState<UpdateShipmentModel>();
  if (errors.packages) {
    if (errors.packages.message) {
      return <Alert severity={"warning"}>{errors.packages.message}</Alert>;
    }
  }
  return null;
};

const BaseInfoForm = (props: { data: ShipmentModel; onFinish?: () => void; onChange: (id: number) => void }) => {
  const values = useMemo<UpdateShipmentModel>(() => {
    return createUpdateShipmentModel(props.data);
  }, [props.data]);

  const [successTimeout, setSuccessTimeout] = useState(0);
  const data = useQueryShipmentMethods();
  const senders =
    useSenderAddressesQuery()?.map(x => {
      return {
        id: `${x.id}`,
        label: `${x.addressName}`,
      };
    }) || [];

  const formContext = useForm<UpdateShipmentModel>({
    values,
  });

  useEffect(() => {
    formContext.reset(values, {
      keepSubmitCount: false,
      keepDirty: false,
      keepErrors: false,
      keepValues: false,
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [values]);

  const { control, watch, handleSubmit, clearErrors, setError, setValue } = formContext;

  const [disabled, setDisabled] = useState(false);

  const { mutateAsync } = useUpdateShipmentMutation(shipmentId => {
    props.onChange(shipmentId);
    setSuccessTimeout(5000);
  });

  const serviceCode = watch("serviceCode");

  const { remove, insert, fields } = useFieldArray({
    control,
    name: "packages",
  });

  const [isCod, isParcel] = (() => {
    if (serviceCode && data) {
      const curr = data.filter(x => x.code === serviceCode);
      return [!!curr[0]?.codAvailable, !!curr[0]?.parcelRequired];
    }
    return [false, false];
  })();

  useEffect(() => {
    setTimeout(() => {
      setValue("hasParcel", isParcel);
      if (serviceCode && ["SMEU", "SMED", "CONN", "COND"].indexOf(serviceCode) > -1) {
        setValue("age", "");
      }
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [isParcel, serviceCode]);

  return (
    <form
      onSubmit={handleSubmit(async fields => {
        setDisabled(true);
        try {
          await mutateAsync({ shipmentId: props.data.id || undefined, data: fields });
        } catch (errors) {
          if (errors instanceof ValidationErrorException) {
            const data = errors.data;
            Object.keys(data.errors).forEach(x => {
              // @ts-ignore
              setError(`${x}`, { type: "validation", message: data.errors[x][0] });
            });
          }
        } finally {
          setDisabled(false);
        }
      })}
    >
      {disabled || !data || !senders ? <SavingProgress /> : null}
      {!props.data.id ? (
        <Alert severity="info">
          Pro úpravu příjemce, odesílatele a případně výběr výdejního místa, je nutno zásilku uložit.
        </Alert>
      ) : null}
      {disabled ? null : (
        <SaveInfo color={"info"} timeout={successTimeout}>
          Základní informace byly uloženy
        </SaveInfo>
      )}
      <FormProvider {...formContext}>
        <Box className="modalBox">
          <Box p={2}>
            <Grid container alignContent={"top"}>
              <Grid item xs={12} md={6}>
                <Box p={1}>
                  <FormLabel>PPL služba</FormLabel>
                  <ShipmentMethodController control={control} name={"serviceCode"} />
                </Box>
                <Box p={1}>
                  <FormLabel>Poznámka</FormLabel>
                  <TextFieldController control={control} name={"note"} />
                </Box>
                <Box p={1}>
                  <FormLabel>Odesílatel (pro etiketu)</FormLabel>
                  <Controller
                    control={control}
                    name="senderId"
                    render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
                      return (
                        <SelectInput
                          key={senders.length}
                          optionals={senders}
                          disableClearable={true}
                          value={`${value || ""}`}
                          onChange={e => {
                            onChange(parseInt(e || "") || undefined);
                          }}
                          error={error?.message}
                          name={"senderId"}
                        />
                      );
                    }}
                  />
                </Box>
                {serviceCode && ["SMEU", "SMED", "CONN", "COND", "COPL"].indexOf(serviceCode) > -1 ? null : (
                  <Box p={1}>
                    <FormLabel>Kontrola věku příjemce</FormLabel>
                    <Controller
                      control={control}
                      name="age"
                      render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
                        return (
                          <SelectInput
                            optionals={[
                              { id: "", label: "Bez kontroly věku" },
                              { id: "15", label: "15+" },
                              { id: "18", label: "18+" },
                            ]}
                            name={"age"}
                            value={`${value || ""}`}
                            onChange={e => {
                              onChange(parseInt(e || "") || "");
                            }}
                            error={error?.message}
                          />
                        );
                      }}
                    />
                  </Box>
                )}
                {isParcel ? (
                  <Box p={1}>
                    <Controller
                      control={control}
                      name="hasParcel"
                      render={({ field: { onChange, value }, fieldState: { error }, formState }) => {
                        return (
                          <>
                            <Switch
                              readOnly={true}
                              checked={!!value}
                              value={value}
                              name={"hasParcel"}
                              onChange={e => {
                                if (isParcel) onChange(isParcel);
                              }}
                            />
                            <FormLabel>Odeslání na parcelshop</FormLabel>
                            {error?.message ? <FormHelperText error={true}>{error?.message}</FormHelperText> : null}
                          </>
                        );
                      }}
                    />
                  </Box>
                ) : null}
                {isCod ? (
                  <>
                    <Box p={1}>
                      <Grid container>
                        <Grid item xs={8}>
                          <FormLabel>Dobírka</FormLabel>
                          <TextFieldController type="number" control={control} name={"codValue"} />
                        </Grid>
                        <Grid item xs={4}>
                          <FormLabel>Měna</FormLabel>
                          <CurrencyController control={control} name={"codValueCurrency"} />
                        </Grid>
                      </Grid>
                    </Box>
                    <Box p={1}>
                      <FormLabel>Dobírka (VS)</FormLabel>
                      <TextFieldController control={control} name={"codVariableNumber"} />
                    </Box>
                    <Box p={1}></Box>
                  </>
                ) : null}
              </Grid>
              <Grid item xs={12} md={6}>
                <Box p={1}>
                  <FormLabel>Zásilky</FormLabel>
                  <ErrorPackage />
                  <TableContainer sx={{ p: 0 }}>
                    <Table>
                      <TableHead>
                        <TableRow>
                          <TableCell width={150}>Reference</TableCell>
                          <TableCell width={150}>Pojištění</TableCell>
                          <TableCell />
                        </TableRow>
                      </TableHead>
                      <TableBody>
                        {fields.map((field, index) => {
                          return (
                            <PackageForm
                              max={fields.length}
                              index={index}
                              key={field.id}
                              add={index => {
                                insert(index, {
                                  referenceId:
                                    fields.map(x => x.referenceId).reverse()[0] || `${props.data.orderId || ""}` || "",
                                });
                              }}
                              remove={index => {
                                remove(index);
                              }}
                            />
                          );
                        })}
                      </TableBody>
                    </Table>
                  </TableContainer>
                </Box>
              </Grid>
            </Grid>
          </Box>
        </Box>
        <Grid container alignItems={"center"}>
          <Grid item xs={6}>
            <Box p={2}>
              <Button
                disabled={disabled}
                onClick={e => {
                  clearErrors();
                }}
                type="submit"
              >
                Uložit
              </Button>
            </Box>
          </Grid>

          <Grid item xs={6} textAlign={"right"}>
            <Box p={2}>
              <Button
                onClick={e => {
                  setDisabled(true);
                  e.preventDefault();
                  props.onFinish?.();
                }}
                type="submit"
              >
                Zavřít
              </Button>
            </Box>
          </Grid>
        </Grid>
      </FormProvider>
    </form>
  );
};

export default BaseInfoForm;
