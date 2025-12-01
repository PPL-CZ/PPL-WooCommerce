import {render, useMemo, useState} from "@wordpress/element";
import {useForm, Controller, useFieldArray, useFormContext, FormProvider} from "react-hook-form";

import {components} from "../schema";
import type {CSSProperties} from "react";

type Data = components["schemas"]["ProductModel"];
type ShipmentMethodModel = components["schemas"]["ShipmentMethodModel"];
type PackageModel = components["schemas"]["PackageSizeModel"];

const Package = (props: {
    position: number,
    remove: (index: number) => void
}) => {
    const {register} = useFormContext<Data>()

    const inputSize = {
        display: "inline",
        width: '5em'
    }

    return <tr>
        <td>
            <input id={`pplXSize`} size={7} style={inputSize}
                   type={"number"} {...register(`pplSizes.${props.position}.xSize`)}/></td>
        <td>
            <input id={`pplYSize`} size={7} style={inputSize}
                   type={"number"} {...register(`pplSizes.${props.position}.ySize`)}/>
        </td>
        <td>
            <input id={`pplZSize`} size={7} style={inputSize}
                   type={"number"} {...register(`pplSizes.${props.position}.zSize`)}/>
        </td>
        <td>
            <a href={'#'} onClick={(ev) => {
                ev.preventDefault();
                props.remove(props.position);
            }}>[x]</a>
        </td>
    </tr>

}

const Tab = (props: { data: Data, methods: ShipmentMethodModel[], pplNonce: string }) => {
    const formCtx = useForm<Data & { pplNonce: string }>({
        defaultValues: {...(props.data || {}), pplNonce: props.pplNonce},
    });

    const {register, control,} = formCtx;

    const float: CSSProperties = {
        float: "none",
        width: "auto"
    }

    const methods = useMemo(() => {
        const methods = props.methods.map(x => x);
        methods.sort((x, y) => {
            return x.title.localeCompare(y.title);
        });
        return methods;
    }, [props.methods])

    const {fields, append, remove} = useFieldArray({
        control,
        name: "pplSizes"
    })

    const thStyle = {
        textAlign: "left"
    }

    return <div className={"options_group"} style={{
        paddingLeft: '162px',
        paddingTop: '1em',
        paddingBottom: '1em'
    }}>
        <div>
            <label style={float}><strong>Věkové omezení (pouze v případě ČR se tato služba poskytuje)</strong></label>
        </div>
        <input type={"hidden"} {...register("pplNonce")} />
        <label style={float} htmlFor={"pplConfirmAge15"}>
            <input style={float} id={"pplConfirmAge15"} type="checkbox" {...register("pplConfirmAge15")} />&nbsp;
            Ověření věku 15+</label>
        <br/>
        <label style={float} htmlFor={"pplConfirmAge18"}>
            <input style={float} id={"pplConfirmAge18"} type="checkbox" {...register("pplConfirmAge18")} />&nbsp;
            Ověření věku 18+</label>
        <br/>
        <label style={float}><strong>Seznam zakázaných míst (pro výdejní místa)</strong></label>
        <div>
            <label style={float}>
                <input id="pplDisabledParcelBox" value={`1`} type={"checkbox"}  {...register('pplDisabledParcelBox')}/>
                &nbsp; ParcelBoxy</label>
        </div>
        <div>
            <label style={float}>
                <input id="pplDisabledAlzaBox" value={`1`} type={"checkbox"}  {...register('pplDisabledAlzaBox')}/>
                &nbsp; AlzaBoxy</label>
        </div>
        <div>
            <label style={float}>
                <input id={`pplDisabledParcelShop`} value={`1`}
                       type={"checkbox"} {...register('pplDisabledParcelShop')}/>
                &nbsp; Obchody</label>
        </div>

        <FormProvider {...formCtx} children={ <div>
            <label style={float}><strong>Produkt lze případně rozdělit na menší balíčky</strong></label>
            <div style={{
                marginLeft: '-150px'
            }}>
                {fields.length ?
                    <table>
                        <thead>
                        <tr>
                            <th style={thStyle}>Šířka (cm)</th>
                            <th style={thStyle}>Výška (cm)</th>
                            <th style={thStyle}>Hloubka (cm)</th>
                            <th/>
                        </tr>
                        </thead>
                        <tbody>
                        {fields.map((item, index) => {
                            return <Package position={index} remove={remove}/>
                        })}
                        </tbody>
                    </table> : null}
                <div>
                    <a href={'#'} onClick={ev => {
                        ev.preventDefault();
                        append({xSize: null, ySize: null, zSize: null});
                    }}>Přidat řádek</a>
                </div>
            </div>
        </div>}/>


        <label style={float}><strong>Seznam zakázaných metod</strong></label><br/>
        {methods.map(shipment => {
            return <Controller
                control={control}
                name={"pplDisabledTransport"}
                render={(props) => {
                    const value = (props.field.value || []);
                    const checked = value.indexOf(shipment.code) > -1;

                    return <div>
                        <label style={float} htmlFor={`pplDisabledTransport_${shipment.code}`}>
                            <input value={`${shipment.code}`} style={float} id={`pplDisabledTransport_${shipment.code}`}
                                   type={"checkbox"}
                                   name={`pplDisabledTransport[]`}
                                   checked={checked}
                                   onChange={x => {
                                       if (value.indexOf(shipment.code) > -1)
                                           props.field.onChange(value.filter(x => x !== shipment.code));
                                       else
                                           props.field.onChange(value.concat([shipment.code]))
                                   }}/>
                            &nbsp; {shipment.title}</label>
                    </div>
                }}
            />
        })}
    </div>
}

export default function product_tab(element, props) {
    const el = element;

    render(<Tab {...props} />, el);
}
