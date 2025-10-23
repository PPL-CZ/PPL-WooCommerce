import React from "react";
import ReactDOM from "react-dom/client";
import App from "./App";
import ThemeContextOverlay from "./components/overlay/ThemeContextOverlay";
import QueryContextOverlay from "./components/overlay/QueryContextOverlay";
import CreateShipmentWidget from "./components/widgets/CreateShipmentWidget";
import CreateShipmentLabelWidget from "./components/widgets/CreateShipmentLabelWidget";
import SelectPrintWidget from "./components/widgets/SelectPrintWidget";
import { WeightsForm } from "./components/forms/WeightsForm/WeightsForm";
import SelectBatchWidget from "./components/widgets/SelectBatchWidget";

const getELement = (element: HTMLElement | string) => {
  if (typeof element === "string") {
    return document.getElementById(element);
  } else if (element) return element;
  return null;
};

const getShadowElement = (element: HTMLElement | string, top: boolean = false) => {
  const attachedElement = getELement(element)!;

  attachedElement!.classList.add("ppl-cz-shadow-dom");
  if (top) {
    attachedElement!.style.zIndex = "15000";
    attachedElement!.style.position = "relative";
  }
  const shadowContainer = attachedElement.attachShadow({ mode: "open" });
  const shadowRootElement = document.createElement("div");

  shadowContainer.appendChild(shadowRootElement);

  return {
    shadowContainer,
    shadowRootElement,
  };
};

const methods = {
  optionsPage: (element: string | HTMLElement) => {
    const { shadowRootElement, shadowContainer } = getShadowElement(element)!;

    const root = ReactDOM.createRoot(shadowRootElement);

    return root.render(
      <React.StrictMode>
        <QueryContextOverlay>
          <ThemeContextOverlay shadowContainer={shadowContainer} shadowRootElement={shadowRootElement}>
            <App />
          </ThemeContextOverlay>
        </QueryContextOverlay>
      </React.StrictMode>
    );
  },
  shipmentSetting: (element: string, args: Record<string, any>) => {
    const rootElement = getELement(element)!;

    const root = ReactDOM.createRoot(rootElement);
    root.render(
      <QueryContextOverlay>
        <WeightsForm data={args.setting} />
      </QueryContextOverlay>
    );

    return {
      rerender: (args: Record<string, any>) => {
        root.render(
          <QueryContextOverlay>
            <WeightsForm data={args.setting} costByWeight={args.costByWeight} />
          </QueryContextOverlay>
        );
      },
      unmount: () => root.unmount(),
    };
  },
  newShipment: (element: string | HTMLElement, args: Record<string, any>) => {
    const { shadowRootElement, shadowContainer } = getShadowElement(element, true)!;

    const root = ReactDOM.createRoot(getELement(shadowRootElement)!);

    root.render(
      <QueryContextOverlay>
        <ThemeContextOverlay shadowContainer={shadowContainer} shadowRootElement={shadowRootElement}>
          <CreateShipmentWidget shipment={args.shipment} onFinish={args.onFinish} onChange={args.onChange} />
        </ThemeContextOverlay>
      </QueryContextOverlay>
    );

    return {
      unmount: () => {
        root.unmount();
        setTimeout(() => {
          shadowContainer.parentElement?.removeChild(shadowContainer);
        }, 500);
      },
    };
  },
  newLabel: (element: string | HTMLElement, args: Record<string, any>) => {
    const { shadowRootElement, shadowContainer } = getShadowElement(element, true)!;

    const root = ReactDOM.createRoot(getELement(shadowRootElement)!);

    root.render(
      <QueryContextOverlay>
        <ThemeContextOverlay shadowContainer={shadowContainer} shadowRootElement={shadowRootElement}>
          <CreateShipmentLabelWidget
            hideOrderAnchor={args.hideOrderAnchor}
            shipments={[{ shipment: args.shipment, errors: {} }]}
            onFinish={args.onFinish}
            onRefresh={args.onRefresh}
          />
        </ThemeContextOverlay>
      </QueryContextOverlay>
    );

    return {
      unmount: () => {
        root.unmount();
        setTimeout(() => {
          shadowContainer.parentElement?.removeChild(shadowContainer);
        }, 500);
      },
    };
  },
  selectBatch: (element: string | HTMLElement, args: Record<string, any>) => {
    const { shadowRootElement, shadowContainer } = getShadowElement(element, true)!;

    const root = ReactDOM.createRoot(getELement(shadowRootElement)!);

    const render = (args: Record<string, any>) => {
      root.render(
        <QueryContextOverlay>
          <ThemeContextOverlay shadowContainer={shadowContainer} shadowRootElement={shadowRootElement}>
            <SelectBatchWidget onClose={args.onClose ?? args.onFinish} items={args.items} />
          </ThemeContextOverlay>
        </QueryContextOverlay>
      );
    };

    render(args);

    return {
      unmount: () => {
        root.unmount();
        setTimeout(() => {
          shadowContainer.parentElement?.removeChild(shadowContainer);
        }, 500);
      },
      render,
    };
  },
  selectLabelPrint: (element: string | HTMLElement, args: Record<string, any>) => {
    const { shadowRootElement, shadowContainer } = getShadowElement(element, true)!;

    const root = ReactDOM.createRoot(getELement(shadowRootElement)!);

    const render = (args: Record<string, any>) => {
      root.render(
        <QueryContextOverlay>
          <ThemeContextOverlay shadowContainer={shadowContainer} shadowRootElement={shadowRootElement}>
            <SelectPrintWidget
              onChange={args.onChange}
              optionals={args.optionals}
              value={args.value}
              onFinish={args.onFinish}
            />
          </ThemeContextOverlay>
        </QueryContextOverlay>
      );
    };

    render(args);

    return {
      unmount: () => {
        root.unmount();
        setTimeout(() => {
          shadowContainer.parentElement?.removeChild(shadowContainer);
        }, 500);
      },
      render,
    };
  },
  newLabels: (element: string | HTMLElement, args: Record<string, any>) => {
    const { shadowRootElement, shadowContainer } = getShadowElement(element, true)!;

    const root = ReactDOM.createRoot(getELement(shadowRootElement)!);

    root.render(
      <QueryContextOverlay>
        <ThemeContextOverlay shadowContainer={shadowContainer} shadowRootElement={shadowRootElement}>
          <CreateShipmentLabelWidget shipments={args.shipments} onFinish={args.onFinish} onRefresh={args.onRefresh} />
        </ThemeContextOverlay>
      </QueryContextOverlay>
    );
    return {
      unmount: () => {
        root.unmount();
        setTimeout(() => {
          shadowContainer.parentElement?.removeChild(shadowContainer);
        }, 500);
      },
    };
  },
};

type InputType = [
  string,
  string | HTMLElement,
  {
    args?: Record<string, any>;
    returnFunc?: (args: Record<string, any>) => void;
  }
];

(function () {
  // @ts-ignore
  const requiredCalls = window.PPLczPlugin || [];

  // @ts-ignore
  const externalMethods = Object.keys(window.PPLczPlugin).reduce((acc, methodName) => {
    if (methodName.match(/^pplcz/)) {
      // @ts-ignore
      acc[methodName] = window.PPLczPlugin[methodName];
    }
    return acc;
  }, {});

  const PPLczPlugin = {
    push: (input: InputType) => {
      const [method, elementId] = input;
      const args = input[2] || {};
      const returnFunc = input[2]?.returnFunc || ((args: Record<string, any>) => {});

      if (!(method in methods) && !(method in PPLczPlugin)) {
        throw new Error(`method ${method} not found`);
      }

      const element = elementId ? getELement(elementId) : null;
      if (!element && elementId) {
        throw new Error(`element ${element} not found`);
      }

      if (method in methods) {
        if (element) {
          // @ts-ignore
          const retData = methods[method](element, args);
          returnFunc(retData);
        } else {
          // @ts-ignore
          const retData = methods[method](args);
          returnFunc(retData);
        }
      } else if (method in PPLczPlugin) {
        if (element) {
          // @ts-ignore
          const retData = PPLczPlugin[method](element, args);
          returnFunc(retData);
        } else {
          // @ts-ignore
          const retData = PPLczPlugin[method](args);
          returnFunc(retData);
        }
      }
    },
    // @ts-ignore
    ...externalMethods,
  };

  // @ts-ignore
  window.PPLczPlugin = PPLczPlugin;

  if (requiredCalls && requiredCalls.length) {
    requiredCalls.forEach((x: InputType) => {
      try {
        PPLczPlugin.push(x);
      } catch (e) {
        console.error(e);
      }
    });
  }
})();

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
