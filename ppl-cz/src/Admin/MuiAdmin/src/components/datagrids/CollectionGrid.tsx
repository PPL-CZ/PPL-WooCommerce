import Menu from "@mui/material/Menu";
import IconButton from "@mui/material/IconButton";
import List from "@mui/material/List";
import ListItemButton from "@mui/material/ListItemButton";
import CircularProgress from "@mui/material/CircularProgress";

import {components} from "../../schema";
import {DataGrid, GridColDef} from "@mui/x-data-grid";
import {useState} from "react";
import MoreVert from "@mui/icons-material/MoreVert";
import {formatDate} from "date-fns";
import { useCollectionOrderMutation } from "../../queries/useCollectionOrderQueries";

type CollectionModel = components["schemas"]["CollectionModel"];


const MenuRow = (props: { id: string, row: CollectionModel }) => {
    const [show, setShow] = useState(false);
    const [progres, setProgres] = useState(false);
    const [anchorEl, setAnchorEl] = useState<HTMLElement | null>(null);
    const state = props.row.state;
    const { mutateAsync } = useCollectionOrderMutation();

    const updateState = async (action: "DELETE" | "PUT") => {
        setProgres(true);
        try {
            await mutateAsync({ collectionId: `${props.row.id}`, action });
        } finally {
            setProgres(false);
        }
    }


    return (
        <>
            {progres ? <CircularProgress size={15}/> : null}
            {["Created", "BeforeSend"].indexOf(props.row.state || "") > -1 ? (
                <>
                    <IconButton
                        id={props.id}
                        onClick={e => {
                            e.stopPropagation();
                            setAnchorEl(e.currentTarget);
                            setShow(true);
                        }}
                    >
                        <MoreVert/>
                    </IconButton>
                    <div>
                        <Menu
                            anchorEl={anchorEl}
                            id="popover"
                            className="wp-reset-div"
                            open={show}
                            onClose={() => {
                                setShow(false);
                            }}
                        >
                            <List component="nav">
                                {state === "BeforeSend" ? (
                                    <ListItemButton
                                        data-collection-type={'send'}
                                        id={'send-' + props.id}
                                        onClick={e => {
                                            updateState("PUT");
                                            setShow(false);
                                        }}
                                    >
                                        Objednat
                                    </ListItemButton>
                                ) : null}
                                {state === "Created" ? (
                                    <>
                                        <ListItemButton
                                            data-collection-type={'delete'}
                                            id={'delete-' + props.id}
                                            onClick={e => {
                                                updateState("DELETE");
                                                setShow(false);
                                            }}
                                        >
                                            Zrušit svoz
                                        </ListItemButton>
                                    </>
                                ) : null}
                            </List>
                        </Menu>
                    </div>
                </>
            ) : null}
        </>
    );
};

const columns: GridColDef<CollectionModel>[] = [
    {
        field: "referenceId",
        width: 250,
        renderHeader: () => <>Reference</>,
        renderCell: value => {
            return value.row.referenceId;
        },
    },
    {
        field: "sendToApiDate",
        width: 120,
        renderHeader: () => <>Objednáno</>,
        renderCell: value => {
            const row = value.row as CollectionModel;
            if (row.sendToApiDate) return formatDate(row.sendToApiDate, "dd.MM.yyyy");
            return "";
        },
    },
    {
        field: "sendDate",
        width: 120,
        renderHeader: () => <>Předání</>,
        renderCell: value => {
            const row = value.row as CollectionModel;
            if (row.sendDate) return formatDate(row.sendDate, "dd.MM.yyyy");
            return "";
        },
    },

    {
        field: "estimatedShipmentCount",
        width: 40,
        renderHeader: () => <>Balíků</>,
        renderCell: value => {
            return value.row.estimatedShipmentCount;
        },
    },
    {
        field: "state",
        width: 200,
        renderHeader: () => <>Stav</>,
        renderCell: value => {
            switch (value.row.state) {
                case "BeforeSend":
                    return "Pred odesláním";
                case "Created":
                    return "Objednaný";
                case "Canceled":
                    return "Zrušený";
            }

            return value.row.state;
        },
    },
    {
        field: "__menu__",
        renderHeader: () => <span/>,
        align: "right",
        flex: 1,
        renderCell: params => {
            return <MenuRow id={`${params.row.id}`} row={params.row}/>;
        },
    },
];

const CollectionGrid = (props: { isLoading: boolean; data: CollectionModel[] | undefined }) => {
    const {data: availableCollections, isLoading} = props;

    return (
        <DataGrid
            rows={availableCollections ?? []}
            loading={isLoading}
            columns={columns}
            autoPageSize={true}
            checkboxSelection
            getRowId={value => {
                return value.id;
            }}
        />
    );
};

export default CollectionGrid;
