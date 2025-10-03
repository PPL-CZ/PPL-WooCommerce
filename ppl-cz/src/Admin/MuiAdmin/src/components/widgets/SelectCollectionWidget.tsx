import Box from "@mui/material/Box";
import Modal from "@mui/material/Modal";
import { components } from "../../schema";
import { useState } from "react";
import NewCollectionForm from "../forms/NewCollectionForm";
import ThemeContextOverlay from "../overlay/ThemeContextOverlay";

type CollectionModel = components["schemas"]["CollectionModel"];

const SelectCollectionWidget = (props: {
  collection?: CollectionModel | null;
  availableCollections: CollectionModel[];
  onSelect: (pplCollectionId: number) => void;
}) => {
  const [collectionId, setCollectionId] = useState(0);
  const [newCollection, setNewCollection] = useState(false);
  const data = props.availableCollections;

  if (props.collection) {
    return (
      <>
        {props.collection.referenceId} (${props.collection.sendDate})
      </>
    );
  } else {
    if (data.length) {
      return (
        <>
          <select
            onClick={e => {
              const value = e.currentTarget.value;
              if (parseInt(value)) {
                setCollectionId(parseInt(value));
              }
            }}
          >
            {data?.map(x => {
              return (
                <option key={x.id} value={`${x.id}`}>
                  {x.referenceId}
                </option>
              );
            })}
          </select>
          {collectionId ? (
            <button
              className="button"
              onClick={e => {
                props.onSelect(collectionId);
              }}
            >
              Použít svoz
            </button>
          ) : null}
          <button
            className="button"
            onClick={e => {
              e.preventDefault();
              setNewCollection(true);
            }}
          >
            Nový svoz
          </button>
          {newCollection ? (
            <Modal
              open={true}
              onClose={() => {
                setNewCollection(false);
              }}
            >
              <NewCollectionForm
                onCreate={(collectionId?: number) => {
                  if (collectionId) setCollectionId(collectionId);
                  setNewCollection(false);
                }}
              />
            </Modal>
          ) : null}
        </>
      );
    } else {
      return (
        <>
          <button
            className="button"
            onClick={e => {
              e.preventDefault();
              setNewCollection(true);
            }}
          >
            Nový svoz
          </button>
          {newCollection ? (
            <ThemeContextOverlay>
              <Modal
                style={{
                  maxWidth: "600px",
                  minWidth: "200px",
                  top: "50%",
                  left: "50%",
                  transform: "translate(-50%, -50%)",
                }}
                open={true}
                onClose={() => {
                  setNewCollection(false);
                }}
              >
                <Box>
                  <NewCollectionForm
                    onCreate={(collectionId?: number) => {
                      if (collectionId) setCollectionId(collectionId);
                      setNewCollection(false);
                    }}
                  />
                </Box>
              </Modal>
            </ThemeContextOverlay>
          ) : null}
        </>
      );
    }
  }
};

export default SelectCollectionWidget;
