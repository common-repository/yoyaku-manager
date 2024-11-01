import { Route, Routes } from "react-router-dom";

import { App } from "@/components/layout";
import { AddEvent } from "./AddEvent";
import { AddEventPeriod } from "./AddEventPeriod";
import { AddTicket } from "./AddTicket";
import { EventDetail } from "./EventDetail";
import { EventPeriodDetail } from "./EventPeriodDetail";
import { Events } from "./Events";
import { UpdateEvent } from "./UpdateEvent";
import { UpdateEventPeriod } from "./UpdateEventPeriod";
import { UpdateTicket } from "./UpdateTicket";

export default () => (
  <App>
    <Routes>
      <Route path="/" element={<Events />} />
      <Route path="/add" element={<AddEvent />} />
      <Route path="/:id" element={<EventDetail />} />
      <Route path="/:id/update" element={<UpdateEvent />} />

      <Route path="/tickets/add" element={<AddTicket />} />
      <Route path="/tickets/:ticketId/update" element={<UpdateTicket />} />

      <Route path="/periods/add/:eventId" element={<AddEventPeriod />} />
      <Route path="/periods/:id" element={<EventPeriodDetail />} />
      <Route path="/periods/:id/update" element={<UpdateEventPeriod />} />
    </Routes>
  </App>
);
