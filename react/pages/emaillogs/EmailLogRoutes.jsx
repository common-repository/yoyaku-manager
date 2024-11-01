import { App } from "@/components/layout";
import { Route, Routes } from "react-router-dom";
import { EmailLogDetail } from "./EmailLogDetail";
import { EmailLogs } from "./EmailLogs";

export default () => (
  <App>
    <Routes>
      <Route path="/" element={<EmailLogs />} />
      <Route path="/:id" element={<EmailLogDetail />} />
    </Routes>
  </App>
);
