import React from "react";
import ReactDOM from "react-dom/client";
import App from "./App";
import ThemeContextOverlay from "./components/overlay/ThemeContextOverlay";
import QueryContextOverlay from "./components/overlay/QueryContextOverlay";
import CreateShipmentWidget from "./components/widgets/CreateShipmentWidget";
import CreateShipmentLabelWidget from "./components/widgets/CreateShipmentLabelWidget";
import SelectPrintWidget from "./components/widgets/SelectPrintWidget";
import {components} from "./schema";
import {WeightsForm} from "./components/forms/WeightsForm/WeightsForm";

const getELement = (element: HTMLElement | string) => {
  if (typeof element === "string") {
    return document.getElementById(element);
  } else if (element) return element;
  return null;
};

let oldStyle: HTMLStyleElement|null = null;
let newStyle: HTMLStyleElement|null = null;

let styleUpdatedFont = false;

const methods = {
  optionsPage: (element: string | HTMLElement) =>
  {
    methods["wpUpdateStyle"]();
    const attachedElement = getELement(element)!
    const el = attachedElement;
    const root = ReactDOM.createRoot(el);


    return ReactDOM.createRoot(el).render(
        <React.StrictMode>
          <QueryContextOverlay>
            <ThemeContextOverlay >
              <App/>
            </ThemeContextOverlay>
          </QueryContextOverlay>
        </React.StrictMode>
    );
  },
  shipmentSetting: (element: string, args: Record<string, any>) => {

    const root = ReactDOM.createRoot(getELement(element)!);
    root.render(
        <QueryContextOverlay>
            <WeightsForm data={args.setting}/>
        </QueryContextOverlay>
    );

    return {
      rerender: (args: Record<string, any>) => {
        root.render(<QueryContextOverlay>
          <WeightsForm  data={args.setting} costByWeight={args.costByWeight}/>
        </QueryContextOverlay>)
      },
      unmount: () => {
        root.unmount()
      },
    };
  },
  newShipment: (element: string | HTMLElement, args: Record<string, any>) => {
    const attachedElement = getELement(element)!
    const el = attachedElement;
    const root = ReactDOM.createRoot(el);


    root.render(
      <QueryContextOverlay>
        <ThemeContextOverlay>
          <CreateShipmentWidget shipment={args.shipment} onFinish={args.onFinish} onChange={args.onChange}/>
        </ThemeContextOverlay>
      </QueryContextOverlay>
    );

    return {
      unmount: () => root.unmount(),
    };
  },
  newLabel: (element: string | HTMLElement, args: Record<string, any>) => {
    methods["wpUpdateStyle"]();

    const attachedElement = getELement(element)!
    const el = attachedElement;
    const root = ReactDOM.createRoot(el);


    root.render(
        <QueryContextOverlay>
          <ThemeContextOverlay>
            <CreateShipmentLabelWidget hideOrderAnchor={args.hideOrderAnchor} shipments={[{ shipment: args.shipment, errors: {} }]} onFinish={args.onFinish} onRefresh={args.onRefresh} />
          </ThemeContextOverlay>
        </QueryContextOverlay>
    );

    return {
      unmount: () => {
        methods["wpUpdateStyleRevert"]();
        root.unmount()
      },
    };
  },

  selectLabelPrint: (element: string|HTMLElement, args: Record<string, any>) => {

    methods["wpUpdateStyle"]();

    const attachedElement = getELement(element)!
    const el = attachedElement;
    const root = ReactDOM.createRoot(el);

    const render = (args: Record<string, any>) => {
      root.render(
          <QueryContextOverlay>
            <ThemeContextOverlay>
              <SelectPrintWidget onChange={args.onChange} optionals={args.optionals} value={args.value} onFinish={args.onFinish}/>
            </ThemeContextOverlay>
          </QueryContextOverlay>
      );
    }

    render(args);

    return {
      unmount: () => {
        methods["wpUpdateStyleRevert"]();
        root.unmount()
      },
      render
    };
  },
  newLabels: (element: string | HTMLElement, args: Record<string, any>) => {
    methods["wpUpdateStyle"]();
    const attachedElement = getELement(element)!
    const el = attachedElement;
    const root = ReactDOM.createRoot(el);


    root.render(
      <QueryContextOverlay>
        <ThemeContextOverlay>
          <CreateShipmentLabelWidget shipments={args.shipments} onFinish={args.onFinish} onRefresh={args.onRefresh} />
        </ThemeContextOverlay>
      </QueryContextOverlay>
    );
    return {
      unmount: () => {
        methods["wpUpdateStyleRevert"]();
        root.unmount();
      }
    };
  },
  wpUpdateStyleRevert: () => {
    if (newStyle && newStyle.parentElement && oldStyle) {
      newStyle.parentElement.insertBefore(oldStyle, newStyle.nextSibling);
      newStyle.parentElement.removeChild(newStyle);
    }
  },
  wpUpdateStyle: () => {

    const head = document.head;
    if (styleUpdatedFont) {
      let link = document.createElement("link");
      link.setAttribute("rel", "preconnect");
      link.setAttribute("href", "https://fonts.googleapis.com");
      head.appendChild(link);
      link.setAttribute("rel", "preconnect");
      link.setAttribute("href", "https://fonts.gstatic.com");
      link.setAttribute("crossorigin", "crossorigin");
      head.appendChild(link);
      link.setAttribute(
          "href",
          "https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
      );
      link.setAttribute("rel", "stylesheet");
      head.appendChild(link);
    }

    styleUpdatedFont = true;

    if (!oldStyle || !newStyle) {
      const link = Array.from(document.getElementsByTagName("link")).find(x =>
          x.getAttribute("href")?.includes("load-styles.php?")
      ) as HTMLLinkElement;

      if (link) {
        oldStyle = link;
        fetch(link.getAttribute("href")!)
            .then(x => x.text())
            .then(stylesText => {
              newStyle = document.createElement("style");
              newStyle.textContent = stylesText;
              link.parentElement?.insertBefore(newStyle, link.nextSibling);
              link.parentElement?.removeChild(link);
              const sheet = newStyle.sheet;
              if (sheet) {
                Array.from(sheet.cssRules).forEach(x => {
                  if ("selectorText" in x && x.selectorText) {
                    const oldText = x.selectorText as string;
                    if (oldText.match(/svg|div|h[0-6]|input|button|(\.|^|\s)p(\.|$|\s)/)) {
                      if (oldText.includes(":") || oldText.includes("components-button")) return;
                      x.selectorText = oldText.split(",").map(x => `${x}:not(.wp-reset-div ${x})`);
                    }
                  }
                  return x;
                });
              }
            });
      }
    } else {
      if (oldStyle.parentElement) {
        oldStyle.parentElement.insertBefore(newStyle, oldStyle.nextSibling);
        oldStyle.parentElement.removeChild(oldStyle);
      }
    }
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
        }
        else {
          // @ts-ignore
          const retData = methods[method](args);
          returnFunc(retData);
        }

      } else if (method in PPLczPlugin) {
        if (element) {
          // @ts-ignore
          const retData = PPLczPlugin[method](element, args);
          returnFunc(retData);
        }
        else {
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
