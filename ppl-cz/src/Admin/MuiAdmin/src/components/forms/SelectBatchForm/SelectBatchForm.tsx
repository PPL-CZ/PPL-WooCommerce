import { useEffect, useState } from "react";
import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import Dialog from "@mui/material/Dialog";
import DialogActions from "@mui/material/DialogActions";
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";
import DialogTitle from "@mui/material/DialogTitle";
import IconButton from "@mui/material/IconButton";
import List from "@mui/material/List";
import ListItem from "@mui/material/ListItem";
import ListItemText from "@mui/material/ListItemText";
import Stack from "@mui/material/Stack";
import Typography from "@mui/material/Typography";
import CloseIcon from "@mui/icons-material/Close";
import { formatDate } from "date-fns";
import { useAddShipments, useBatchs } from "../../../queries/useBatchQueries";
import { components } from "../../../schema";

type BatchModel = components["schemas"]["BatchModel"];
type PrepareShipmentBatchModel = components["schemas"]["PrepareShipmentBatchModel"];

interface SelectBatchFormProps {
  items: PrepareShipmentBatchModel;
  onContinue?: () => void;
}

export const SelectBatchForm = ({ onContinue, items }: SelectBatchFormProps) => {
  const { data: freeBatches, isLoading } = useBatchs(true);
  const [selectedBatch, setSelectedBatch] = useState<BatchModel | null>(null);
  const [saving, setSaving] = useState(false);
  const addShipments = useAddShipments();

  useEffect(() => {
    if (!isLoading && freeBatches) {
      if (freeBatches.length === 1) {
        setSelectedBatch(freeBatches[0]);
      }
    }
  }, [freeBatches, isLoading]);

  const handleGoToBatch = async (batchId?: string) => {
      setSaving(true);
    try {
      const id = await addShipments.mutateAsync({
        batchId,
        items,
      });

      const a = document.createElement("a");
      a.href = "?page=pplcz_options&#/batch/" + id;
      document.location = a.href;
    } finally {
      setSaving(false);
    }
  };

  const handleStayOnPage = async (batchId?: string) => {
      setSaving(true);
    try {
      await addShipments.mutateAsync({
        batchId,
        items,
      });
      onContinue?.();
    } finally {
      setSaving(false);
    }
  };

  if (isLoading) {
    return (
      <Dialog open={true} onClose={onContinue} aria-labelledby="single-batch-dialog-title">
        <DialogTitle id="single-batch-dialog-title">
          <IconButton
            onClick={onContinue}
            sx={{
              position: "absolute",
              right: 8,
              top: 8,
            }}
          >
            <CloseIcon />
          </IconButton>
        </DialogTitle>
        <DialogContent>
          <Box p={2}>
            <Typography>Načítání volných dávek...</Typography>
          </Box>
        </DialogContent>
      </Dialog>
    );
  }

  // Více než jedna dávka - zobrazit seznam
  if ((freeBatches?.length ?? 0) > 1) {
    return (
      <Dialog open={true} onClose={onContinue} aria-labelledby="single-batch-dialog-title">
        <DialogTitle id="single-batch-dialog-title">
          Vyberte dávku
          <IconButton
            onClick={onContinue}
            sx={{
              position: "absolute",
              right: 8,
              top: 8,
            }}
          >
            <CloseIcon />
          </IconButton>
        </DialogTitle>
        <DialogContent>
          <List>
            {freeBatches!.map(batch => (
              <ListItem
                key={batch.id}
                sx={{
                  border: "1px solid #e0e0e0",
                  borderRadius: 1,
                  mb: 1,
                }}
              >
                <ListItemText
                  primary={`Dávka #${batch.id}`}
                  secondary={
                    <>
                      {batch.remoteBatchId ? `BatchID: ${batch.remoteBatchId} | ` : ""}
                      Vytvořeno: {batch.created ? formatDate(batch.created, "dd.MM.yyyy") : "N/A"}
                    </>
                  }
                />
                <Stack direction="row" spacing={1}>
                  <Button variant="contained" size="small" onClick={() => handleGoToBatch(`${batch.id}`)}>
                    Přidat k tisku a připravit tisk
                  </Button>
                  <Button variant="outlined" size="small" onClick={() => handleStayOnPage(`${batch.id}`)}>
                    Přidat k tisku a zůstat na stránce
                  </Button>
                </Stack>
              </ListItem>
            ))}
              <ListItem
                  key={'new'}
                  sx={{
                      border: "1px solid #e0e0e0",
                      borderRadius: 1,
                      mb: 1,
                  }}
              >
                  <ListItemText
                      primary={`Nová dávka`}
                      secondary={
                          <>

                          </>
                      }
                  />
                  <Stack direction="row" spacing={1}>
                      <Button disabled={saving} variant="contained" size="small" onClick={() => handleGoToBatch()}>
                          Přidat k tisku a připravit tisk
                      </Button>
                      <Button disabled={saving} variant="outlined" size="small" onClick={() => handleStayOnPage()}>
                          Přidat k tisku a zůstat na stránce
                      </Button>
                  </Stack>
              </ListItem>
          </List>
        </DialogContent>
      </Dialog>
    );
  }

  // Jedna dávka - dialog
  return (
    <Dialog open={true} onClose={onContinue} aria-labelledby="single-batch-dialog-title">
      {selectedBatch && (
        <DialogTitle id="single-batch-dialog-title">
          Nalezena volná dávka
          <IconButton
            onClick={onContinue}
            sx={{
              position: "absolute",
              right: 8,
              top: 8,
            }}
          >
            <CloseIcon />
          </IconButton>
        </DialogTitle>
      )}
      <DialogContent>
        {selectedBatch && (
          <>
            <DialogContentText>Byla nalezena pouze jedna volná dávka:</DialogContentText>
            <Box mt={2} p={2} sx={{ bgcolor: "grey.100", borderRadius: 1 }}>
              <Typography variant="body1">
                <strong>Dávka #{selectedBatch.id}</strong>
              </Typography>
              {selectedBatch.created && (
                <Typography variant="body2">Vytvořeno: {formatDate(selectedBatch.created, "dd.MM.yyyy")}</Typography>
              )}
            </Box>
          </>
        )}
        {!selectedBatch && (
          <>
            <DialogContentText>Vytvořit novou dávku k tisku</DialogContentText>
            <IconButton
              onClick={onContinue}
              sx={{
                position: "absolute",
                right: 8,
                top: 8,
              }}
            >
              <CloseIcon />
            </IconButton>
          </>
        )}
      </DialogContent>
      <DialogActions>
        <Button disabled={saving} onClick={() => handleStayOnPage(selectedBatch?.id ? `${selectedBatch?.id}`: undefined)} color="secondary">
          Přidat k tisku a zůstat na stránce
        </Button>
        <Button disabled={saving} onClick={() => handleGoToBatch(selectedBatch?.id ? `${selectedBatch?.id}`: undefined)} variant="contained" color="primary">
          Připravit tisk
        </Button>
      </DialogActions>
    </Dialog>
  );
};

export default SelectBatchForm;
