import { components } from "../../schema";
import { DataGrid, GridColDef } from "@mui/x-data-grid";
import { formatDate } from "date-fns";
import { Link } from "@mui/material";
import { useNavigate } from "react-router-dom";

type BatchModel = components["schemas"]["BatchModel"];

const MenuRow = (props: { id: string; row: BatchModel }) => {
  return <></>;
};

const RenderId = (props: { id: number | string }) => {
  const navigate = useNavigate();
  return (
    <Link
      data-key={"batch-id"}
      href={"#"}
      onClick={ev => {
        ev.preventDefault();
        navigate(`/batch/${props.id}`);
      }}
    >
      {props.id}
    </Link>
  );
};

const RenderRemoteBatchId = (props: { id: number | string; remoteBatchId?: string | null }) => {
  const navigate = useNavigate();
  if (!props.remoteBatchId) return null;
  return (
    <Link
      data-key={"batch-remote-id"}
      href={"#"}
      onClick={ev => {
        ev.preventDefault();
        navigate(`/batch/${props.id}`);
      }}
    >
      {props.remoteBatchId}
    </Link>
  );
};

const columns: GridColDef<BatchModel>[] = [
  {
    field: "id",
    renderHeader: () => <>Id</>,
    renderCell: value => {
      return <RenderId id={value.id} />;
    },
  },
  {
    field: "batchId",
    renderHeader: () => <>BatchId</>,
    renderCell: value => {
      return <RenderRemoteBatchId id={value.id} remoteBatchId={value.row.remoteBatchId} />;
    },
  },
  {
    field: "created",

    renderHeader: () => <>Vytvořeno</>,
    renderCell: value => {
      const row = value.row as BatchModel;
      if (row.created) return formatDate(row.created, "dd.MM.yyyy");
      return "";
    },
  },

  {
    field: "lock",

    renderHeader: () => <>Objednáno</>,
    renderCell: value => {
      return value.row.lock ? "Ano" : "Ne";
    },
  },
  {
    field: "__menu__",
    renderHeader: () => <span />,
    align: "right",
    flex: 1,
    renderCell: params => {
      return <MenuRow id={`${params.row.id}`} row={params.row} />;
    },
  },
];

const BatchGrid = (props: { isLoading: boolean; data: BatchModel[] | undefined }) => {
  const { data: availableBatchs, isLoading } = props;

  return (
    <DataGrid
      rows={availableBatchs ?? []}
      loading={isLoading}
      columns={columns}
      autoPageSize={true}
      autoHeight={true}
      checkboxSelection
      getRowId={value => {
        return value.id!;
      }}
    />
  );
};

export default BatchGrid;
