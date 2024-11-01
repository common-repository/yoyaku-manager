import { createRoot } from "@wordpress/element";
import { StrictMode } from "react";

import BookingsRoutes from "./bookings/BookingsRoutes";
import CustomerRoutes from "./customers/CustomerRoutes";
import EmailLogRoutes from "./emaillogs/EmailLogRoutes";
import EventRoutes from "./events/EventRoutes";
import NotificationRoutes from "./notifications/NotificationRoutes";
import SettingRoutes from "./settings/SettingRoutes";
import WorkerRoutes from "./workers/WorkerRoutes";

const renderYoyakuPage = (domName, renderNode) => {
  if (
    "undefined" !== typeof document.getElementById(domName) &&
    null !== document.getElementById(domName)
  ) {
    const root = createRoot(document.getElementById(domName));
    root.render(<StrictMode>{renderNode}</StrictMode>);
  }
};

document.addEventListener("DOMContentLoaded", () => {
  renderYoyakuPage("yoyaku-bookings", <BookingsRoutes />);
  renderYoyakuPage("yoyaku-customers", <CustomerRoutes />);
  renderYoyakuPage("yoyaku-events", <EventRoutes />);
  renderYoyakuPage("yoyaku-emaillogs", <EmailLogRoutes />);
  renderYoyakuPage("yoyaku-notifications", <NotificationRoutes />);
  renderYoyakuPage("yoyaku-settings", <SettingRoutes />);
  renderYoyakuPage("yoyaku-workers", <WorkerRoutes />);
});
