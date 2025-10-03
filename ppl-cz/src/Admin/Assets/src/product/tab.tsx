import {render, useMemo, useState} from "@wordpress/element";
import {useForm, Controller} from "react-hook-form";

import { components } from "../schema";
import {CSSProperties} from "react";

type Data = components["schemas"]["ProductModel"];
type ShipmentMethodModel = components["schemas"]["ShipmentMethodModel"];

const Tab = (props: { data:  Data, methods: ShipmentMethodModel[], pplNonce: string })=> {
    const { register, control } = useForm<Data & {pplNonce: string }>({
        defaultValues: {...(props.data ||{}), pplNonce: props.pplNonce },
    });
    const float: CSSProperties = {
        float: "none",
        width: "auto"
    }

    const methods = useMemo(()=> {
        const methods  = props.methods.map(x => x);
        methods.sort((x,y) => {
            return x.title.localeCompare(y.title);
        });
        return methods;
    },[props.methods])


    return <p className={"form-field"}>
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
    </p>
}

export default function product_tab(element, props) {
    const el = element;

    render(<Tab {...props} />, el);
}
