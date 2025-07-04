import {Fragment, useEffect, useMemo, useState} from "react";
import {components} from "../../../schema";
import {useForm, Controller, useFieldArray} from "react-hook-form";

export const WeightsForm = (props: {
    data: components["schemas"]["ShipmentMethodSettingModel"],
    costByWeight?: boolean
}) => {

    const {getValues, control, setValue} = useForm<components["schemas"]["ShipmentMethodSettingModel"]>({
        values: props.data
    });

    const [currencies, setCurrencies] = useState(() => {
        return props.data
            .currencies
            .filter(x => x.enabled)
            .map(x => x.currency)
    });

    const addCurrency = (currency: string) => {

        let newCurrencies = currencies;

        const value = getValues();


        if (!value.currencies.some(x => x.currency === currency)) {
            value.currencies = value.currencies.concat([{
                currency,
                enabled: false
            }]);
        }

        value.weights = value.weights.map(x => {

            if (x.prices.some(y => y.currency === currency))
                return x;
            x.prices = x.prices.concat([{
                currency
            }]);

            return {
                ...x
            };
        })

        setValue("currencies", value.currencies);
        setValue("weights", value.weights);
        setCurrencies([...newCurrencies, currency]);
    }

    console.debug(getValues());

    const allCurrencies = props.data.currencies.map(x => x.currency);

    const freeCurrencies = useMemo(() => allCurrencies.filter(x => currencies.indexOf(x) == -1), [allCurrencies, currencies])


    const [selected, setSelected] = useState("CZK")

    const inputName = (basename: string, currency: string) => {
        return `woocommerce_${props.data.code}_${basename}_${currency}`;
    }
    const inputWeightName = (...args: (string | number)[]) => `woocommerce_${props.data.code}_weights${args.map(x => `[${x}]`).join('')}`;

    const values = getValues();


    const {fields, append, remove} = useFieldArray({
        control,
        name: "weights"
    });

    const costByWeight = props.costByWeight ?? props.data.costByWeight;

    const isLineThrough = (is: boolean) => {
        if (is)
            return {
                textDecoration: "line-through"
            }
        return {}
    }

    const isHidden = (is: boolean) => {
        if (is)
            return {
                display: "none"
            }
        return {};
    }

    return <>
        <table  className={"wc_input_table widefat"}>
            <thead>
            <tr>
                <th colSpan={currencies.length + 1}>
                    {currencies.map(((x, index) => {
                        return <Fragment>
                            {index === 0 ? null : <>&nbsp;|&nbsp;</>}
                            <a href={`#${x}`} style={{
                                fontWeight: selected === x ? "bold" : "normal"
                            }} onClick={e => {
                                e.preventDefault();
                                setSelected(x);
                            }}>{x}</a>
                        </Fragment>
                    }))}&nbsp;
                    {freeCurrencies.length > 0 ?
                        <select key={freeCurrencies.join('')} onChange={e => {
                            if (e.target.value) {
                                addCurrency(e.target.value);
                                setSelected(e.target.value);
                            }
                        }}>
                            <option value={""} selected={true}>Přidat měnu</option>
                            {(freeCurrencies ?? []).map(x => <option value={x}>{x}</option>)}
                        </select> : null}
                </th>
            </tr>
            </thead>
            {currencies.map(currency => {
                const style = (() => currency === selected ? {} : {display: 'none'})();
                const selectedCurrenciesIndex = values.currencies.findIndex(x => x.currency === currency);
                return <tbody style={style}>
                <tr>
                    <th style={{width: "8%"}}>
                        Povolení měny
                    </th>
                    <td className={"compound"}>
                        <Controller key={selectedCurrenciesIndex} control={control}
                                    render={({field, fieldState, formState}) => {
                                        const val = field.value
                                        return <input type="checkbox" className="checkbox"
                                                      name={inputName("cost_allow", currency)} onChange={x => {
                                            field.onChange(!val);
                                        }} value="1" checked={!!val}/>
                                    }} name={`currencies.${selectedCurrenciesIndex}.enabled`}/>
                    </td>
                </tr>
                <tr>
                    <th>
                        Od jaké ceny bude doprava zadarmo
                    </th>
                    <td>
                        <Controller key={selectedCurrenciesIndex} control={control}
                                    render={({field, fieldState, formState}) => {
                                        const val = field.value
                                        return <input type="text" name={inputName("cost_order_free", currency)}
                                                      onChange={x => {
                                                          field.onChange(x);
                                                      }} value={val ?? ''}/>
                                    }} name={`currencies.${selectedCurrenciesIndex}.costOrderFree`}/>
                    </td>
                </tr>
                {['CZK', "EUR", "PLN", "HUF", "RON"].indexOf(selected) > -1 ? <>
                    <tr>
                        <th>
                            Příplatek za dobírku
                        </th>
                        <td>
                            <Controller key={selectedCurrenciesIndex} control={control}
                                        render={({field, fieldState, formState}) => {
                                            const val = field.value
                                            return <input type="text" name={inputName("cost_cod_fee", currency)}
                                                          onChange={x => {
                                                              field.onChange(x);
                                                          }} value={val ?? ''}/>
                                        }} name={`currencies.${selectedCurrenciesIndex}.costCodFee`}/>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Příplatek i v případě bezplatné dopravy u dobírky
                        </th>
                        <td className={"compound"}>
                            <Controller key={selectedCurrenciesIndex} control={control}
                                        render={({field, fieldState, formState}) => {
                                            const val = field.value
                                            return <input type="checkbox" className="checkbox"
                                                          name={inputName("cost_cod_fee_always", currency)}
                                                          onChange={x => {
                                                              field.onChange(!val);
                                                          }} value="1" checked={!!val}/>
                                        }} name={`currencies.${selectedCurrenciesIndex}.costCodFeeAlways`}/>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Od jaké ceny bude doprava zadarmo pro dobírku
                        </th>
                        <td>
                            <Controller key={selectedCurrenciesIndex} control={control}
                                        render={({field, fieldState, formState}) => {
                                            const val = field.value
                                            return <input type="text" name={inputName("cost_order_free_cod", currency)}
                                                          onChange={x => {
                                                              field.onChange(x);
                                                          }} value={val ?? ''}/>
                                        }} name={`currencies.${selectedCurrenciesIndex}.costOrderFreeCod`}/>
                        </td>
                    </tr>
                </> : null}
                <tr style={isHidden(!!costByWeight)}>
                    <th>Cena za dopravu</th>
                    <td>
                    <Controller key={selectedCurrenciesIndex} control={control}
                                render={({field, fieldState, formState}) => {
                                    const val = field.value
                                    return <input type="number" name={inputName("cost", currency)}
                                                  onChange={x => {
                                                      field.onChange(x);
                                                  }} value={val ?? ''}/>
                                }} name={`currencies.${selectedCurrenciesIndex}.cost`}
                    />
                    </td>
                </tr>
                </tbody>
            })}
        </table>
        <h3 style={isHidden(!costByWeight)}>Ceny za dopravu a váhu</h3>
        <p style={({...isHidden(!costByWeight), ...{ marginTop: "1em", marginBottom: "1em"}})}>
            Tato funkce umožňuje automatické stanovení ceny dopravy na základě celkové hmotnosti objednávky. Po překročení definovaných hmotnostních hranic se cena dopravy upravuje dle nastavených pravidel. Nevyplněný parametr Od se bere jako 0 (do jako maximum), proto je nutné vždy stanovit koncovou hranici váhy pro správný výpočet ceny.
            V případě, že váha zásilky odpovídá více pravidlům, vybírá se nejdražší doprava. Váha do je brána jako ostrá nerovnost, např. bude nastaveno na 5kg, pak to bude {'<'}5kg
        </p>
        <table style={isHidden(!costByWeight)} className={"wc_input_table widefat"}>
            <thead>
            <tr>
                <th rowSpan={2}>
                    Váha&nbsp;od (kg)
                </th>
                <th rowSpan={2}>
                    Váha&nbsp;do (kg)
                </th>
                {props.data.parcelBoxes ?
                    <th rowSpan={2}>
                        Blokovaná výdejní místa
                    </th> : null}
                {currencies.length ?
                    <th colSpan={currencies.length + 1}>
                        Cena
                    </th> : null}

            </tr>
            <tr>

                {currencies.filter(x => x === selected).map(x => <th key={x}>{x}</th>)}
                {currencies.filter(x => x !== selected).map(x => <th key={x}>{x}</th>)}
                <th style={{width: '50px'}}/>
            </tr>

            </thead>
            <tbody>
            {fields.map((row, index) => {

                return <tr key={index}>
                    <td>
                        <Controller control={control} render={({field, fieldState, formState}) => {
                            const val = field.value
                            return <input type="number" name={inputWeightName(index, "from")}
                                          onChange={x => {
                                              field.onChange(x);
                                          }} value={val ?? ''}/>
                        }} name={`weights.${index}.from`}/>
                    </td>
                    <td>
                        <Controller control={control} render={({field, fieldState, formState}) => {
                            const val = field.value
                            return <input type="number" name={inputWeightName(index, "to")}
                                          onChange={x => {
                                              field.onChange(x);
                                          }} value={val ?? ''}/>
                        }} name={`weights.${index}.to`}/>
                    </td>
                    {props.data.parcelBoxes ?
                        <td>
                            <Controller control={control}
                                        render={({field, fieldState, formState}) => {
                                            const val = field.value
                                            return <><input type="checkbox" className="checkbox"
                                                            name={inputWeightName(index, "disabledParcelBox")}
                                                            onChange={x => {
                                                                field.onChange(!val);
                                                            }} value="1" checked={!!val}/> <span style={isLineThrough(!!val)}>Parcelboxy</span></>
                                            }} name={`weights.${index}.disabledParcelBox`}/><br/>

                            <Controller control={control}
                                        render={({field, fieldState, formState}) => {
                                            const val = field.value
                                            return <><input type="checkbox" className="checkbox"
                                                            name={inputWeightName(index, "disabledAlzaBox")}
                                                            onChange={x => {
                                                                field.onChange(!val);
                                                            }} value="1" checked={!!val}/> <span style={isLineThrough(!!val)}>Alzaboxy</span></>
                                            }} name={`weights.${index}.disabledAlzaBox`}/><br/>

                            <Controller control={control}
                                        render={({field, fieldState, formState}) => {
                                            const val = field.value
                                            return <><input type="checkbox" className="checkbox"
                                                          name={inputWeightName(index, "disabledParcelShop")}
                                                          onChange={x => {
                                                              field.onChange(!val);
                                                          }} value="1" checked={!!val}/> <span style={isLineThrough(!!val)}>ParcelShopy</span></>
                                        }} name={`weights.${index}.disabledParcelShop`}/>
                        </td> : null}
                    {currencies.filter(x => x === selected).concat(currencies.filter(x => x !== selected)).map(x => {
                        const index2 = row.prices.findIndex(y => y.currency === x);
                        return <td key={x}>
                            <Controller control={control} render={({field, fieldState, formState}) => {
                                const val = field.value
                                return <input type="number" name={inputWeightName(index, "prices", index2, "price")}
                                              onChange={x => {
                                                  field.onChange(x);
                                              }} value={val ?? ''}/>
                            }} name={`weights.${index}.prices.${index2}.price`}/>
                            <Controller control={control} render={({field, fieldState, formState}) => {
                                return <input type="hidden" name={inputWeightName(index, "prices", index2, "currency")}
                                              value={field.value}/>
                            }} name={`weights.${index}.prices.${index2}.currency`}/>
                        </td>
                    })}
                    <td style={{
                        padding: "3px"
                    }}>
                        <button className={"button"} onClick={e => {
                            e.preventDefault();
                            remove(index);
                        }}>Smazat řádek
                        </button>

                    </td>
                </tr>
            })}
            <tr>
                <td colSpan={currencies.length + 3} style={{
                    padding: "3px"
                }}>
                    <button className={"button"} onClick={e => {
                        e.preventDefault();
                        append({
                            prices: currencies.map(x => ({
                                currency: x,
                            }))
                        });
                    }}>Nový řádek
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

    </>
}