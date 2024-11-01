import { App } from "@/components/layout";
import { Route, Routes } from "react-router-dom";
import { AddBooking } from "./AddBooking";
import { BookingDetail } from "./BookingDetail";
import { Bookings } from "./Bookings";
import { UpdateBooking } from "./UpdateBooking";

export default () => (
  <App>
    <Routes>
      <Route path="/" element={<Bookings />} />
      <Route path="/add/:periodId" element={<AddBooking />} />
      <Route path="/:id" element={<BookingDetail />} />
      <Route path="/:id/update" element={<UpdateBooking />} />
    </Routes>
  </App>
);
