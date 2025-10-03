export default {
  breakpoints: {
    keys: ["xs", "sm", "gsm", "md", "gmd", "lg", "xl", "xxl"],
    values: { xs: 0, sm: 600, gsm: 768, md: 900, gmd: 1024, lg: 1200, xl: 1536, xxl: 1920 },
    unit: "px",
  },
  palette: {
    mode: "light",
    common: { black: "#000", white: "#fff" },
    primary: { main: "#004B93", dark: "rgb(0, 52, 102)", light: "rgb(49, 110, 168)", contrastText: "#EEF5FA" },
    secondary: { main: "#123456", dark: "rgb(12, 36, 60)", light: "rgb(64, 91, 118)", contrastText: "#EEF5FA" },
    error: { main: "#e1313d", dark: "rgb(157, 34, 42)", light: "rgb(230, 89, 98)", contrastText: "#EEF5FA" },
    warning: { main: "#e15b31", dark: "rgb(157, 63, 34)", light: "rgb(230, 122, 89)", contrastText: "#EEF5FA" },
    info: { main: "#4b7ca5", dark: "rgb(52, 86, 115)", light: "rgb(110, 149, 182)", contrastText: "#EEF5FA" },
    success: { main: "#429e9d", dark: "rgb(46, 110, 109)", light: "rgb(102, 176, 176)", contrastText: "#EEF5FA" },
    grey: {
      "50": "#f1f1f0",
      "100": "#dcddda",
      "200": "#c5c6c2",
      "300": "#adafaa",
      "400": "#9c9d97",
      "500": "#8a8c85",
      "600": "#82847d",
      "700": "#777972",
      "800": "#6d6f68",
      "900": "#5a5c55",
      A100: "#edfbcc",
      A200: "#ddf79d",
      A400: "#d2ff62",
      A700: "#faffff",
    },
    contrastThreshold: 3,
    tonalOffset: 0.2,
    text: { primary: "#004B93", secondary: "#004B93", disabled: "#809DB3" },
    divider: "#dde7ee",
    background: { paper: "#ffffff", default: "#f5fcff" },
    action: {
      active: "rgba(0, 0, 0, 0.54)",
      hover: "#EEF5FA",
      hoverOpacity: 0.04,
      selected: "rgba(0, 0, 0, 0.08)",
      selectedOpacity: 0.08,
      disabled: "#809DB3",
      disabledBackground: "#C9D7DF",
      disabledOpacity: 0.38,
      focus: "rgba(0, 0, 0, 0.12)",
      focusOpacity: 0.12,
      activatedOpacity: 0.12,
    },
  },
  typography: {
    fontFamily: "Roboto,proxima-nova,sans-serif",
    fontWeightRegular: 300,
    fontWeightMedium: 600,
    fontWeightBold: 800,
    h1: {
      fontSize: "1.5rem",
      lineHeight: 1.2857142857142858,
      fontWeight: 600,
      letterSpacing: 0,
      fontFamily: "Roboto,proxima-nova,sans-serif",
      "@media (min-width:1200px)": { fontSize: "1.75rem" },
    },
    h2: {
      fontSize: "1.3333333333333333rem",
      lineHeight: 1.3333333333333333,
      fontWeight: 600,
      letterSpacing: 0,
      fontFamily: "Roboto,proxima-nova,sans-serif",
      "@media (min-width:1200px)": { fontSize: "1.5rem" },
    },
    h3: {
      fontSize: "1.1666666666666667rem",
      lineHeight: 1.2,
      fontWeight: 600,
      letterSpacing: 0,
      fontFamily: "Roboto,proxima-nova,sans-serif",
      "@media (min-width:1200px)": { fontSize: "1.25rem" },
    },
    h4: { fontSize: 16, lineHeight: 1.5, fontWeight: 600, fontFamily: "Roboto,proxima-nova,sans-serif" },
    h5: { fontSize: 16, lineHeight: 1.5, fontWeight: 600, fontFamily: "Roboto,proxima-nova,sans-serif" },
    h6: { fontSize: 14, lineHeight: 1.4285714285714286, fontWeight: 600, fontFamily: "Roboto,proxima-nova,sans-serif" },
    body1: {
      fontSize: 16,
      lineHeight: 1.5,
      fontWeight: 300,
      letterSpacing: 0,
      fontFamily: "Roboto,proxima-nova,sans-serif",
    },
    body2: {
      fontSize: 14,
      fontWeight: 300,
      letterSpacing: 0,
      fontFamily: "Roboto,proxima-nova,sans-serif",
      lineHeight: 1.43,
    },
    button: {
      fontSize: 16,
      fontWeight: 300,
      letterSpacing: 0,
      textTransform: "none",
      fontFamily: "Roboto,proxima-nova,sans-serif",
      lineHeight: 1.75,
    },
    htmlFontSize: 16,
    fontSize: 14,
    fontWeightLight: 300,
    subtitle1: { fontFamily: "Roboto,proxima-nova,sans-serif", fontWeight: 300, fontSize: "1rem", lineHeight: 1.75 },
    subtitle2: {
      fontFamily: "Roboto,proxima-nova,sans-serif",
      fontWeight: 600,
      fontSize: "0.875rem",
      lineHeight: 1.57,
    },
    caption: { fontFamily: "Roboto,proxima-nova,sans-serif", fontWeight: 300, fontSize: "0.75rem", lineHeight: 1.66 },
    overline: {
      fontFamily: "Roboto,proxima-nova,sans-serif",
      fontWeight: 300,
      fontSize: "0.75rem",
      lineHeight: 2.66,
      textTransform: "uppercase",
    },
    inherit: {
      fontFamily: "inherit",
      fontWeight: "inherit",
      fontSize: "inherit",
      lineHeight: "inherit",
      letterSpacing: "inherit",
    },
  },
  components: {
    MuiAlert: {
      defaultProps: {
        variant: "filled",
        iconMapping: {
          error: {
            type: { type: {}, compare: null },
            key: null,
            ref: null,
            props: {},
            _owner: null,
            _store: { validated: true },
          },
        },
        closeText: "Zavřít",
      },
      styleOverrides: {
        root: { borderColor: "transparent" },
        icon: { fontSize: "1.5rem", "& svg": { fontSize: "inherit", color: "inherit" } },
        message: { lineHeight: 1.5, fontSize: 16 },
        filledError: {
          boxShadow:
            "0px 3px 3px -2px rgba(0,0,0,0.2),0px 3px 4px 0px rgba(0,0,0,0.14),0px 1px 8px 0px rgba(0,0,0,0.12)",
          color: "#EEF5FA",
        },
        filledWarning: {
          boxShadow:
            "0px 3px 3px -2px rgba(0,0,0,0.2),0px 3px 4px 0px rgba(0,0,0,0.14),0px 1px 8px 0px rgba(0,0,0,0.12)",
          color: "#EEF5FA",
        },
        filledInfo: {
          boxShadow:
            "0px 3px 3px -2px rgba(0,0,0,0.2),0px 3px 4px 0px rgba(0,0,0,0.14),0px 1px 8px 0px rgba(0,0,0,0.12)",
          color: "#EEF5FA",
        },
        filledSuccess: {
          boxShadow:
            "0px 3px 3px -2px rgba(0,0,0,0.2),0px 3px 4px 0px rgba(0,0,0,0.14),0px 1px 8px 0px rgba(0,0,0,0.12)",
          color: "#EEF5FA",
        },
        outlinedError: { borderColor: "currentColor", color: "#e1313d" },
        outlinedWarning: { borderColor: "currentColor", color: "#e15b31", "& $icon": { color: "inherit" } },
        outlinedInfo: { borderColor: "currentColor", color: "#4b7ca5" },
        outlinedSuccess: { borderColor: "currentColor", color: "#429e9d" },
      },
    },
    MuiAvatar: {
      defaultProps: {},
      styleOverrides: { colorDefault: { color: "#EEF5FA", backgroundColor: "rgb(46, 110, 109)" } },
    },
    MuiAutocomplete: {
      defaultProps: {
        fullWidth: true,
        ChipProps: { variant: "outlined" },
        clearText: "Vymazat",
        closeText: "Zavřít",
        loadingText: "Načítání…",
        noOptionsText: "Žádné možnosti",
        openText: "Otevřít",
      },
      styleOverrides: {
        inputRoot: { gap: 3 },
        tag: { margin: 0 },
        clearIndicator: { fontSize: 20, "& .MuiSvgIcon-root": { fontSize: "inherit" } },
      },
    },
    MuiBadge: {
      defaultProps: { color: "error" },
      styleOverrides: {
        root: { fontSize: "1.5rem", color: "#4b7ca5", "& svg": { fontSize: "inherit", color: "inherit" } },
        badge: { color: "#EEF5FA", backgroundColor: "rgb(102, 176, 176)" },
        colorPrimary: { color: "#EEF5FA", backgroundColor: "rgb(49, 110, 168)" },
        colorSecondary: { color: "#EEF5FA", backgroundColor: "rgb(64, 91, 118)" },
        colorError: { color: "#EEF5FA", backgroundColor: "rgb(230, 89, 98)" },
      },
    },
    MuiButtonBase: {
      defaultProps: {},
      styleOverrides: {
        root: {
          "&.Mui-error:hover": { backgroundColor: "rgb(230, 89, 98)" },
          "&.Mui-success:hover": { backgroundColor: "rgb(102, 176, 176)" },
          "&.Mui-info:hover": { backgroundColor: "rgb(110, 149, 182)" },
        },
      },
    },
    MuiButton: {
      defaultProps: { variant: "contained", color: "primary" },
      styleOverrides: {
        root: { borderRadius: 0 },
        contained: {},
        containedPrimary: { "&:hover": { backgroundColor: "rgb(49, 110, 168)" } },
        containedSecondary: { "&:hover": { backgroundColor: "rgb(64, 91, 118)" } },
        startIcon: { "& > *:nth-of-type(1)": {} },
        sizeSmall: { paddingTop: 5, paddingBottom: 3, fontSize: "0.875rem" },
        sizeLarge: { paddingTop: 7.5, paddingBottom: 5.5, fontSize: "1.25rem" },
      },
    },
    MuiButtonGroup: {
      defaultProps: {},
      styleOverrides: { groupedContained: { "&:not(:last-of-type)": { borderColor: "currentColor" } } },
    },
    MuiContainer: { defaultProps: { maxWidth: false } },
    MuiDataGrid: {
      defaultProps: {},
      styleOverrides: {
        root: { lineHeight: 1.5, fontSize: 16, border: 0 },
        cell: { padding: "0 8px", borderColor: "#dde7ee", "&:focus-within": { outline: 0 } },
        row: { "&:hover": { backgroundColor: "#EEF5FA" } },
        columnHeaderTitle: { fontWeight: 600 },
        columnHeader: { padding: "0 8px", "&:focus-within": { outline: 0 } },
        columnHeaderCheckbox: { padding: 0 },
        columnHeaders: { borderBottom: "1px solid currentColor" },
        overlay: { zIndex: 1 },
      },
    },
    MuiCheckbox: {
      defaultProps: { color: "primary" },
      styleOverrides: {
        root: { ".MuiSvgIcon-fontSizeSmall": { fontSize: 20 }, ".MuiSvgIcon-fontSizeMedium": { fontSize: 24 } },
      },
    },
    MuiChip: {
      defaultProps: {
        color: "primary",
        deleteIcon: { type: { type: {}, compare: null }, key: null, ref: null, props: {}, _owner: null, _store: {} },
      },
      styleOverrides: {
        root: {
          height: 28,
          lineHeight: "20px",
          fontSize: 14,
          "&.MuiChip-colorDefault": { color: "#EEF5FA", backgroundColor: "#429e9d" },
          "&.Mui-disabled": {
            color: "#809DB3",
            backgroundColor: "#C9D7DF",
            opacity: 1,
            "& .MuiChip-avatar": { opacity: 0.3 },
          },
        },
        label: { paddingRight: 10, paddingLeft: 10 },
        avatar: { width: 20, height: 20, "&.MuiChip-avatarColorDefault": { color: "#EEF5FA" } },
        clickable: { "&.MuiChip-colorDefault:not(.MuiChip-outlined):hover": { backgroundColor: "rgb(102, 176, 176)" } },
        clickableColorPrimary: { "&:hover": { backgroundColor: "rgb(49, 110, 168)" } },
        clickableColorSecondary: { "&:hover": { backgroundColor: "rgb(64, 91, 118)" } },
        icon: { width: 20, height: 20, color: "#EEF5FA" },
        deleteIcon: {
          width: 20,
          height: 20,
          display: "flex",
          justifyContent: "center",
          alignItems: "center",
          borderRadius: "50%",
          color: "inherit",
          padding: 3,
          boxShadow: "inset 0 0 0 1.5px",
          "&:hover": { boxShadow: "none" },
          "&.MuiChip-deleteIconColorDefault:hover": { color: "#429e9d", backgroundColor: "#EEF5FA" },
        },
        deleteIconColorPrimary: { "&:hover": { color: "#004B93", backgroundColor: "#EEF5FA" } },
        deleteIconColorSecondary: { "&:hover": { color: "#123456", backgroundColor: "#EEF5FA" } },
        outlined: {
          "& .MuiChip-deleteIcon": { boxShadow: "none", "&:hover": { boxShadow: "inset 0 0 0 1.5px" } },
          "&.Mui-disabled": { backgroundColor: "transparent", borderColor: "#C9D7DF" },
          "& .MuiChip-icon": { color: "inherit" },
          "&.MuiChip-colorDefault:not(.Mui-disabled)": { color: "#429e9d", background: "none" },
          "&.MuiChip-colorDefault.MuiChip-clickable": { "&:hover": { backgroundColor: "#EEF5FA" } },
          "& .MuiChip-deleteIconOutlinedColorDefault": {
            color: "#EEF5FA",
            backgroundColor: "#429e9d",
            "&:hover": { color: "#429e9d", backgroundColor: "#EEF5FA" },
          },
        },
        deleteIconOutlinedColorPrimary: {
          color: "#EEF5FA",
          backgroundColor: "#004B93",
          "&:hover": { color: "#004B93", backgroundColor: "#EEF5FA" },
        },
        deleteIconOutlinedColorSecondary: {
          color: "#EEF5FA",
          backgroundColor: "#123456",
          "&:hover": { color: "#123456", backgroundColor: "#EEF5FA" },
        },
      },
    },
    MuiFab: {
      defaultProps: { color: "secondary", size: "small" },
      styleOverrides: {
        root: {
          boxShadow:
            "0px 3px 1px -2px rgba(0,0,0,0.2),0px 2px 2px 0px rgba(0,0,0,0.14),0px 1px 5px 0px rgba(0,0,0,0.12)",
          "& svg": { fontSize: "1.25rem" },
          "&:hover": {
            boxShadow:
              "0px 2px 4px -1px rgba(0,0,0,0.2),0px 4px 5px 0px rgba(0,0,0,0.14),0px 1px 10px 0px rgba(0,0,0,0.12)",
          },
          "&.MuiFab-success:hover": { backgroundColor: "rgb(102, 176, 176)" },
          "&.MuiFab-info:hover": { backgroundColor: "rgb(110, 149, 182)" },
        },
        primary: { "&:hover": { backgroundColor: "rgb(49, 110, 168)" } },
        secondary: { "&:hover": { backgroundColor: "rgb(64, 91, 118)" } },
        extended: { "&": { gap: "8px" }, "&.MuiFab-sizeSmall": { paddingRight: 12 } },
      },
    },
    MuiFormControl: {
      defaultProps: { color: "primary", fullWidth: true, size: "small" },
      styleOverrides: { root: { "& .MuiFormHelperText-root": { color: "#8a8c85" } } },
    },
    MuiFormLabel: {
      defaultProps: { color: "primary" },
      styleOverrides: { root: { marginBottom: "4px", fontSize: 14, fontWeight: 800, color: "rgb(12, 36, 60)" } },
    },
    MuiGrid: { defaultProps: { spacing: 2 }, styleOverrides: {} },
    MuiGrid2: { defaultProps: { spacing: 2 }, styleOverrides: {} },
    MuiIcon: { defaultProps: { fontSize: "inherit" }, styleOverrides: {} },
    MuiIconButton: {
      defaultProps: { color: "primary", size: "large" },
      styleOverrides: {
        sizeSmall: { "& > svg": { fontSize: 16 } },
        sizeMedium: { "& > svg": { fontSize: 20 } },
        sizeLarge: { "& > svg": { fontSize: 24 } },
      },
    },
    MuiInputAdornment: {
      defaultProps: {},
      styleOverrides: { root: { color: "inherit" }, positionStart: { marginRight: 0 }, positionEnd: { marginLeft: 0 } },
    },
    MuiInputBase: {
      defaultProps: {},
      styleOverrides: {
        root: {
          "&.MuiOutlinedInput-root": {
            borderColor: "#8a8c85",
            "& fieldset": { borderColor: "#8a8c85" },
            "&.Mui-focused fieldset": { borderColor: "#8a8c85" },
            input: { color: "black" },
            "& .MuiFormHelperText-root": { color: "black" },
          },
        },
      },
    },
    MuiInputLabel: {
      defaultProps: {},
      styleOverrides: {
        animated: { color: "#809DB3", fontSize: "inherit", fontWeight: "unset", marginBottom: 0 },
        filled: { color: "#809DB3" },
      },
    },
    MuiInput: { defaultProps: {}, styleOverrides: { root: {} } },
    MuiList: { defaultProps: {}, styleOverrides: {} },
    MuiListItem: {
      defaultProps: {},
      styleOverrides: {
        button: {
          "&.Mui-selected": { backgroundColor: "#EEF5FA", "&:hover": { backgroundColor: "#EEF5FA" } },
          "&:hover": { backgroundColor: "#EEF5FA" },
        },
      },
    },
    MuiListItemIcon: {
      defaultProps: {},
      styleOverrides: {
        root: { minWidth: 40, fontSize: "1.5rem", color: "#004B93", "& > .MuiSvgIcon-root": { color: "inherit" } },
      },
    },
    MuiListItemText: { defaultProps: {}, styleOverrides: {} },
    MuiListItemButton: { defaultProps: {}, styleOverrides: {} },
    MuiMenu: { defaultProps: {}, styleOverrides: {} },
    MuiMenuItem: { defaultProps: {}, styleOverrides: {} },
    MuiOutlinedInput: {
      defaultProps: {},
      styleOverrides: {
        root: {
          "&.Mui-focused": { backgroundColor: "#fff" },
          "&:not(.Mui-disabled):not(.Mui-focused):hover": { backgroundColor: "#EEF5FA" },
        },
        notchedOutline: { borderColor: "#004B93" },
      },
    },
    MuiPagination: {
      defaultProps: { "aria-label": "Navigace stránkováním" },
      styleOverrides: { root: { fontSize: "inherit" } },
    },
    MuiPaginationItem: { defaultProps: {}, styleOverrides: {} },
    MuiPaper: {
      defaultProps: { variant: "outlined" },
      styleOverrides: {
        root: { borderRadius: 0, boxShadow: "0px 2px 16px 0px #0000001A", borderColor: "#DFE7EF", borderWidth: "2px" },
      },
    },
    MuiPopover: {
      defaultProps: {},
      styleOverrides: {
        paper: {
          boxShadow:
            "0px 3px 5px -1px rgba(0,0,0,0.2),0px 5px 8px 0px rgba(0,0,0,0.14),0px 1px 14px 0px rgba(0,0,0,0.12)",
        },
      },
    },
    MuiRadio: { defaultProps: { color: "primary" }, styleOverrides: {} },
    MuiSelect: {
      defaultProps: { color: "primary" },
      styleOverrides: {
        select: {
          lineHeight: "28px",
          verticalAlign: "middle",
          paddingTop: 6,
          paddingBottom: 6,
          "& > *": { marginRight: "8px" },
        },
        icon: { fontSize: "1.5rem", color: "#004B93" },
      },
    },
    MuiSlider: {
      defaultProps: { color: "primary", size: "small" },
      styleOverrides: {
        thumb: { width: 16, height: 16 },
        valueLabel: { top: 0, backgroundColor: "transparent", color: "inherit", fontSize: 14 },
      },
    },
    MuiStack: { defaultProps: { spacing: 2 } },
    MuiSvgIcon: {
      defaultProps: {},
      styleOverrides: {
        fontSizeSmall: { fontSize: 16 },
        fontSizeMedium: { fontSize: 20 },
        fontSizeLarge: { fontSize: 24 },
      },
    },
    MuiStep: { defaultProps: {}, styleOverrides: { root: { lineHeight: 1.5, fontSize: 16, color: "#123456" } } },
    MuiStepConnector: {
      defaultProps: {},
      styleOverrides: {
        root: { color: "#809DB3", "&.Mui-active": { color: "#123456" }, "&.Mui-completed": { color: "#123456" } },
        line: { borderColor: "currentColor" },
      },
    },
    MuiStepIcon: {
      defaultProps: {},
      styleOverrides: {
        root: {
          width: "100%",
          height: "100%",
          fontSize: "1.5rem",
          color: "inherit",
          fill: "#123456",
          "&.Mui-completed": { color: "inherit" },
          ".Mui-disabled &": { color: "#809DB3", fill: "transparent" },
        },
        text: {
          fontSize: 10,
          transform: "translateY(-0.5px)",
          fill: "#EEF5FA",
          ".Mui-disabled &": { fill: "currentColor" },
        },
      },
    },
    MuiStepLabel: {
      defaultProps: {},
      styleOverrides: {
        root: { color: "inherit", "&.Mui-disabled": { color: "#809DB3" } },
        label: {
          lineHeight: "inherit",
          fontSize: "inherit",
          color: "inherit",
          "&.Mui-active": { color: "inherit" },
          "&.Mui-completed": { color: "inherit" },
        },
        iconContainer: {
          width: 44,
          height: 44,
          display: "flex",
          justifyContent: "center",
          alignItems: "center",
          marginRight: "8px",
          paddingRight: 0,
          color: "#EEF5FA",
          backgroundColor: "#123456",
          border: "1px solid #123456",
          borderRadius: "50%",
          "&.Mui-completed": { color: "#EEF5FA", backgroundColor: "#123456" },
          "&.Mui-disabled": { backgroundColor: "transparent", borderColor: "#809DB3" },
        },
        labelContainer: { color: "inherit" },
      },
    },
    MuiStepper: {
      defaultProps: {},
      styleOverrides: {
        root: {
          paddingRight: 0,
          paddingLeft: 0,
          border: "none",
          background: "transparent",
          ".MuiStep-root": { color: "#004B93", textTransform: "uppercase" },
          ".MuiStepLabel-iconContainer": { borderColor: "#004B93", backgroundColor: "#004B93", color: "#fff" },
          ".MuiStepIcon-root": { fill: "#004B93" },
          ".MuiStepLabel-iconContainer.Mui-completed": { background: "#fff" },
          ".MuiStepConnector-root.Mui-active": { color: "#004B93" },
          ".MuiStepConnector-root.Mui-completed": { color: "#004B93" },
        },
      },
    },
    MuiSwitch: {
      defaultProps: { color: "default" },
      styleOverrides: {
        root: {
          width: 42,
          height: 26,
          padding: 0,
          margin: "8px",
          "&::before, &::after": {
            position: "absolute",
            top: 10,
            lineHeight: 1,
            fontSize: 8,
            color: "#fff",
            opacity: 0.5,
          },
          "&::before": { content: '"O"', right: 8 },
          "&::after": { content: '"|"', left: 7 },
        },
        track: { backgroundColor: "#4b7ca5", borderRadius: 13, opacity: 1 },
        switchBase: {
          padding: 3,
          "&.Mui-checked": { color: "#fff", transform: "translateX(15px)", "& + .MuiSwitch-track": { opacity: 1 } },
          "&.Mui-disabled, &.Mui-disabled.Mui-checked": {
            color: "#fff",
            "& + .MuiSwitch-track": { opacity: 1, backgroundColor: "#C9D7DF" },
          },
          "&.MuiSwitch-colorDefault:not(.Mui-disabled)": {
            "&.Mui-checked": { "& + .MuiSwitch-track": { backgroundColor: "#429e9d" } },
          },
        },
        sizeSmall: {
          width: 32,
          height: 20,
          padding: 0,
          "&::before": { display: "none" },
          "&::after": { display: "none" },
          "& .MuiSwitch-thumb": { width: 12, height: 12 },
          "& .MuiSwitch-switchBase.Mui-checked": { transform: "translateX(12px) !important" },
        },
      },
    },
    MuiTablePagination: {
      defaultProps: { labelRowsPerPage: "Řádků na stránce:" },
      styleOverrides: {
        root: { fontSize: "inherit" },
        input: { width: "auto", lineHeight: "inherit", marginBottom: 0 },
      },
    },
    MuiTextField: {
      defaultProps: { color: "primary", fullWidth: true },
      styleOverrides: { root: { borderRadius: 0 } },
    },
    MuiTooltip: {
      defaultProps: { placement: "top-start", arrow: true },
      styleOverrides: {
        tooltip: {
          padding: "0.4rem 0.6rem",
          fontSize: 14,
          fontWeight: 300,
          color: "#EEF5FA",
          backgroundColor: "#4b7ca5",
          border: "1px solid rgb(71, 117, 156)",
          borderRadius: "2px",
          filter: "drop-shadow(0 2px 3px rgb(0,0,0,0.3))",
        },
        tooltipPlacementTop: { "@media (min-width:600px)": { marginTop: "8px", marginBottom: "8px" } },
        arrow: { "&:before": { backgroundColor: "rgb(67, 111, 148)" } },
      },
    },
    MuiTab: {
      defaultProps: {},
      styleOverrides: {
        root: {
          fontSize: 14,
          color: "#004B93",
          textTransform: "uppercase",
          "&:hover": { backgroundColor: "#EEF5FA" },
          "&.Mui-disabled": { color: "#809DB3" },
        },
      },
    },
    MuiTabs: {
      defaultProps: { textColor: "primary", indicatorColor: "primary" },
      styleOverrides: { root: { minHeight: "unset" } },
    },
    MuiTable: { defaultProps: {}, styleOverrides: { root: { fontSize: 16 } } },
    MuiTableContainer: {
      defaultProps: {},
      styleOverrides: {
        root: { paddingTop: "24px", paddingBottom: "24px", paddingRight: "32px", paddingLeft: "32px" },
      },
    },
    MuiTableRow: { defaultProps: {}, styleOverrides: {} },
    MuiTableCell: {
      defaultProps: {},
      styleOverrides: {
        root: {
          paddingTop: 13.5,
          paddingBottom: 13.5,
          lineHeight: "inherit",
          fontSize: "inherit",
          borderBottom: "1px solid #dde7ee",
        },
        head: { borderColor: "currentcolor" },
      },
    },
    MuiTypography: { defaultProps: {}, styleOverrides: { gutterBottom: { marginBottom: "16px" } } },
    MuiListSubheader: {
      defaultProps: {},
      styleOverrides: { root: { color: "#123456", lineHeight: "24px", fontWeight: 100, fontSize: "1rem" } },
    },
    MuiBreadcrumbs: { defaultProps: { expandText: "Ukázat cestu" } },
    MuiRating: { defaultProps: { emptyLabelText: "Prázdné" } },
  },
  shape: { borderRadius: 0 },
};
