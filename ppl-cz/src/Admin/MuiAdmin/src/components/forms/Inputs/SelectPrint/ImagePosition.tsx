
import Box from "@mui/material/Box"
import imagePath from "../../../../assets/imagePath";
import ListImage from "../../../../assets/list.png";
import {useImagePositionStyle} from "./style";
import {useEffect, useMemo, useState} from "react";

const getPosition = (x: number, y: number, width: number, height: number) => {
    const hwidth = width / 2;
    const hheight = height / 2
    if (x > hwidth) {
        if (y > hheight)
            return 2;
        return 1;
    } else {
        if (y > hheight)
            return 4;
        return 3;
    }
}

const highlightRectangle =  (width: number, height:number, position: number) =>
{

    let polygon = [ 0,0, 0.5,0 , 0.5,0.5, 1,0.5 , 1,1, 0,1]

    switch (position)
    {
        case 2:
            polygon = [ 0,0, 1,0, 1,0.5, 0.5,0.5, 0.5,1, 0,1];
            break;
        case 3:
            polygon = [ 0,.5, 0.5,0.5, 0.5,0, 1,0, 1,1, 0,1];
            break;
        case 4:
            polygon = [ 0,0, 1,0, 1,1, 0.5,1, 0.5,0.5,  0,0.5];
            break;
    }

    return `polygon(${polygon.reduce((acc, cur, index) => {
        if (index % 2) {
            const val = `${cur * height}px`;
            return `${acc} ${val}, `;
        }
        return `${acc} ${cur * width}px `;
    }, "").replace(/,\s*$/, '')})`;
}

const ImagePosition = (props: { position: number, onChange: (val: number) =>void, width: number, height: number }) => {
    const styles = useImagePositionStyle();
    const [position, setPosition] = useState(props.position);

    useEffect(() => {
        setPosition(props.position);
    }, [props.position]);

    const mask = useMemo( () => {
        return highlightRectangle(props.width , props.height, position);
    }, [props.width, props.height, position]);

    return <Box className={styles.classes.container} sx={{
        position: 'relative',
        width: props.width,
        height: props.height,
    }}
                onMouseOut={e=>{
                    if (props.position !== position)
                        setPosition(props.position);
                }}
                onClick={e=>{

                    // @ts-ignore
                    const rect = e.target.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const newposition = getPosition(x, y, props.width, props.height);
                    if (newposition !== position) {
                        setPosition(newposition);
                        props.onChange(newposition);
                    }

                }}
    >
        <img className={styles.classes.image} src={imagePath(ListImage)} width={props.width} height={props.height} />
        <div

            className={styles.classes.mask}
            style={{
                clipPath: mask
            }} />
    </Box>
}

export default ImagePosition;