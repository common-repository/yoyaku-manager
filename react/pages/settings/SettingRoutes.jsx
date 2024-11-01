import { Route, Routes } from "react-router-dom";
import { App } from "@/components/layout";
import { Settings } from "./Settings";

export default () => (
  <App>
    <Routes>
      <Route path="/" element={<Settings />} />
    </Routes>
  </App>
);
