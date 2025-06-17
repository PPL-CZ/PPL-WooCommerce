import React from "react";

import LogPage from "./pages/LogPage";
import SettingPage from "./pages/SettingPage";
import CollectionsPage from "./pages/CollectionsPage";
import NewsPage from "./pages/NewsPage";
import { createHashRouter, RouterProvider } from "react-router-dom";
import NewCollectionsPage from "./pages/NewCollectionPage";
import LinksPage from "./pages/LinksPage";

const router = createHashRouter([
  {
    path: "logs",
    element: <LogPage />,
  },
  {
    path: "setting",
    element: <SettingPage />,
  },
  {
    path: "collection/new",
    element: <NewCollectionsPage />,
  },
  {
    path: "news",
    element: <NewsPage />,
  },
  {
    path: "links",
    element: <LinksPage />
  },
  {
    path: "*",
    element: <CollectionsPage />,
  },
]);

function App() {
  return <RouterProvider router={router} />;
}

export default App;
