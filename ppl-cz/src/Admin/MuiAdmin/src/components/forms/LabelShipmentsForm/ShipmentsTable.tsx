import Table from "@mui/material/Table";
import TableBody from "@mui/material/TableBody";
import TableCell from "@mui/material/TableCell";
import TableHead from "@mui/material/TableHead";
import TableRow from "@mui/material/TableRow";
import { DndProvider } from "react-dnd";
import { HTML5Backend } from "react-dnd-html5-backend";
import { FieldArrayWithId, UseFieldArrayMove } from "react-hook-form";
import Item from "./Item";
import { SortingMenu } from "./SortingMenu";
import { CreteLabelShipmentItems } from "./types";
import { tableConfig } from "./tableConfig";
import { useRef, useState } from "react";
import { useTableStyle } from "./styles";
import { Alert } from "@mui/material";

interface ShipmentsTableProps {
  batchId: string;
  fields: FieldArrayWithId<CreteLabelShipmentItems, "items", "id">[];
  isLocked: boolean;
  hideOrderAnchor?: boolean;
  move: UseFieldArrayMove;
  onReorder: (reverse: boolean) => void;
}

export const ShipmentsTable = ({
  batchId,
  fields,
  isLocked,
  hideOrderAnchor,
  onReorder,
  move,
}: ShipmentsTableProps) => {
  const draggedPosition = useRef<number | null>(null);
  const styles = useTableStyle();
  const [flashId, setFlashId] = useState<number | null>(null);
  const locked = isLocked && !!fields?.[0]?.shipment?.batchRemoteId;
  const hasManyRows = fields?.length > 1;

  return (
    <>
      {!locked && hasManyRows ? (
        <Alert icon={false} severity="info">
          🖱️ Chyť řádek za grip ikonu (⋮⋮) a přetáhni ho, pokud chceš jinak seřadit tisk etiket.
        </Alert>
      ) : null}
      <DndProvider backend={HTML5Backend}>
        <Table size={tableConfig.table.size} className={styles.classes.hover}>
          <TableHead>
            <TableRow>
              <TableCell sx={tableConfig.header.cell}>
                Objednávka
                {!locked && hasManyRows ? <SortingMenu onSort={onReorder} /> : null}
              </TableCell>
              <TableCell sx={tableConfig.header.cell}>Adresa</TableCell>
              <TableCell sx={tableConfig.header.cell}>Služba</TableCell>
              <TableCell sx={tableConfig.header.cell}>Balíků</TableCell>
              <TableCell sx={tableConfig.header.cell}>Dobírka</TableCell>
              <TableCell sx={tableConfig.header.cell}>VS</TableCell>
              {isLocked ? null : <TableCell sx={tableConfig.header.cell}></TableCell>}
            </TableRow>
          </TableHead>
          <TableBody>
            {fields.map((field, index) => (
              <Item
                key={field.shipment.id}
                batchId={batchId}
                maxRows={fields.length}
                hideOrderAnchor={hideOrderAnchor}
                position={index}
                isLocked={isLocked}
                flashId={flashId}
                setFlashId={setFlashId}
                draggedPosition={draggedPosition}
                move={move}
              />
            ))}
          </TableBody>
        </Table>
      </DndProvider>
    </>
  );
};
