import { Route, Routes } from "react-router-dom";

import { App } from "@/components/layout";
import { Notifications } from "./Notifications";
import { UpdateNotification } from "./UpdateNotification";

export default () => (
  <App>
    <Routes>
      <Route path="/" element={<Notifications />} />
      <Route path="/:id/update" element={<UpdateNotification />} />
    </Routes>
  </App>
);
