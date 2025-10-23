import { useState } from "react";
import IconButton from "@mui/material/IconButton";
import List from "@mui/material/List";
import ListItemButton from "@mui/material/ListItemButton";
import Menu from "@mui/material/Menu";
import { MoreVert } from "@mui/icons-material";
import { tableConfig } from "./tableConfig";

interface ItemActionsMenuProps {
  position: number;
  isLocked: boolean;
  onRemove: () => void;
  onShowDetail: () => void;
}

export const ItemActionsMenu = ({ position, isLocked, onRemove, onShowDetail }: ItemActionsMenuProps) => {
  const [anchorEl, setAnchorEl] = useState<HTMLElement | null>(null);
  const [isOpen, setIsOpen] = useState(false);

  const handleOpen = (event: React.MouseEvent<HTMLButtonElement>) => {
    event.stopPropagation();
    setAnchorEl(event.currentTarget);
    setIsOpen(true);
  };

  const handleClose = () => {
    setIsOpen(false);
  };

  const handleRemove = () => {
    onRemove();
    handleClose();
  };

  const handleShowDetail = () => {
    onShowDetail();
    handleClose();
  };

  return (
    <>
      <IconButton id={`${position}`} size={tableConfig.icon.size} onClick={handleOpen}>
        <MoreVert fontSize={tableConfig.icon.fontSize} />
      </IconButton>
      <Menu
        anchorEl={anchorEl}
        id="item-actions-menu"
        className="wp-reset-div"
        open={isOpen}
        onClose={handleClose}
      >
        <List component="nav">
          {!isLocked ? (
            <ListItemButton onClick={handleRemove}>
              Vyřadit z tisku
            </ListItemButton>
          ) : null}
          <ListItemButton onClick={handleShowDetail}>
            Detail
          </ListItemButton>
        </List>
      </Menu>
    </>
  );
};
