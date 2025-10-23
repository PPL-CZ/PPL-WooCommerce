import { useState } from "react";
import IconButton from "@mui/material/IconButton";
import List from "@mui/material/List";
import ListItemButton from "@mui/material/ListItemButton";
import Menu from "@mui/material/Menu";
import { MoreVert } from "@mui/icons-material";
import { tableConfig } from "./tableConfig";

interface SortingMenuProps {
  onSort: (reverse: boolean) => void;
}

export const SortingMenu = ({ onSort }: SortingMenuProps) => {
  const [anchorEl, setAnchorEl] = useState<HTMLElement | null>(null);
  const [isOpen, setIsOpen] = useState(false);

  const handleOpen = (event: React.MouseEvent<HTMLElement>) => {
    setAnchorEl(event.target as HTMLElement);
    setIsOpen(true);
  };

  const handleClose = () => {
    setIsOpen(false);
  };

  const handleSortAscending = () => {
    handleClose();
    onSort(false);
  };

  const handleSortDescending = () => {
    handleClose();
    onSort(true);
  };

  return (
    <>
      <IconButton size={tableConfig.icon.size} onClick={handleOpen}>
        <MoreVert fontSize={tableConfig.icon.fontSize} />
      </IconButton>
      <Menu
        anchorEl={anchorEl}
        id="sorting-menu"
        className="wp-reset-div"
        open={isOpen}
        onClose={handleClose}
      >
        <List component="nav">
          <ListItemButton onClick={handleSortAscending}>
            Řadit vzestupně
          </ListItemButton>
          <ListItemButton onClick={handleSortDescending}>
            Řadit sestupně
          </ListItemButton>
        </List>
      </Menu>
    </>
  );
};
