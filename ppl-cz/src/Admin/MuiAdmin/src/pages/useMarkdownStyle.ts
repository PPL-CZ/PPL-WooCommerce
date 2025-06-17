import {makeStyles} from "tss-react/mui";
import Box from "@mui/material/Box";
import Markdown from "react-markdown";
import {Fragment} from "react";

export const useMarkdownStyle = makeStyles()(theme => {
    return {
        markdown: {
            'img' : {
              display: "inline",
              height: "2em",
              marginRight: '1em',
              width: "auto",
                position: "relative",
                top: "0.5em"
            },
            'backgroundColor': 'white',
            'p, li': {
                fontSize: '1.2em',
                lineHeight: '1.3em',
                marginBottom: "1em"
            },
            'p': {
                whiteSpace: "pre-line",

            },
            'li': {
                listStyle: "outside",
                marginLeft: '1em',
                '&.none': {
                    listStyle: 'none'
                }

            },
            'textarea': {
                color: 'black',
                fontSize: '1em',
            },
            'textarea:focus': {
                boxShadow: 'none'
            }
        },
    }
});

export default useMarkdownStyle;