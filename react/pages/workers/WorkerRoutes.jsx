import { Route, Routes } from "react-router-dom";

import { App } from "@/components/layout";
import { Workers } from "./Workers";

export default () => (
  <App>
    <Routes>
      <Route path="/" element={<Workers />} />
    </Routes>
  </App>
);
