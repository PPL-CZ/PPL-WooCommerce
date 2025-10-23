import { SxProps, Theme } from "@mui/material";

/**
 * Dostupné velikostní varianty pro tabulku
 */
export type TableSizeVariant = "compact" | "normal" | "comfortable";

/**
 * Konfigurace stylů pro jednotlivé varianty
 */
interface TableStyleConfig {
  table: {
    size: "small" | "medium";
  };
  header: {
    cell: SxProps<Theme>;
  };
  body: {
    cell: SxProps<Theme>;
  };
  icon: {
    size: "small" | "medium" | "large";
    fontSize: "small" | "medium" | "large" | "inherit";
  };
}

/**
 * Definice jednotlivých variant
 */
const tableConfigs: Record<TableSizeVariant, TableStyleConfig> = {
  compact: {
    table: {
      size: "small",
    },
    header: {
      cell: {
        fontSize: "0.875rem", // 14px
        padding: "8px",
      },
    },
    body: {
      cell: {
        fontSize: "0.813rem", // 13px
        padding: "6px 8px",
      },
    },
    icon: {
      size: "small",
      fontSize: "small",
    },
  },
  normal: {
    table: {
      size: "medium",
    },
    header: {
      cell: {
        fontSize: "1rem", // 16px
        padding: "12px 16px",
      },
    },
    body: {
      cell: {
        fontSize: "0.875rem", // 14px
        padding: "10px 16px",
      },
    },
    icon: {
      size: "medium",
      fontSize: "medium",
    },
  },
  comfortable: {
    table: {
      size: "medium",
    },
    header: {
      cell: {
        fontSize: "1.125rem", // 18px
        padding: "16px 20px",
      },
    },
    body: {
      cell: {
        fontSize: "1rem", // 16px
        padding: "14px 20px",
      },
    },
    icon: {
      size: "large",
      fontSize: "large",
    },
  },
};

/**
 * Aktuálně zvolená varianta (lze měnit podle potřeby)
 */
const CURRENT_VARIANT: TableSizeVariant = "compact";

/**
 * Exportovaná konfigurace pro použití v komponentách
 */
export const tableConfig = tableConfigs[CURRENT_VARIANT];

/**
 * Helper funkce pro snadné přepínání variant
 */
export const getTableConfig = (variant: TableSizeVariant = CURRENT_VARIANT): TableStyleConfig => {
  return tableConfigs[variant];
};
