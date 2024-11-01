import { settings } from "@/utils/settings";
import { __ } from "@wordpress/i18n";
import { Button } from "react-bootstrap";

export const EventBookingListBtn = ({ eventId }) => {
  const href = `${window.location.pathname}?page=yoyaku-bookings#/?eventId=${eventId}`;
  return (
    <Button variant="outline-secondary" href={href}>
      {__("Booking List", "yoyaku-manager")}
    </Button>
  );
};

export const EventPeriodBookingListLink = ({ eventPeriodId }) => {
  // Button の href引数 を使うと下線が表示されるためonClickで遷移させる
  const onClick = () => {
    window.location = `${window.location.pathname}?page=yoyaku-bookings#/?eventPeriodId=${eventPeriodId}`;
  };
  return (
    <Button variant="link" className="p-0" onClick={onClick}>
      {__("Booking List", "yoyaku-manager")}
    </Button>
  );
};

export const AddBookingLink = ({ periodId }) => {
  // Button の href引数 を使うと下線が表示されるためonClickで遷移させる
  const onClick = () => {
    window.location = `${window.location.pathname}?page=yoyaku-bookings#/add/${periodId}`;
  };

  if (!settings.canWrite()) return null;

  return (
    <Button variant="link" className="p-0" onClick={onClick}>
      {__("Add Booking", "yoyaku-manager")}
    </Button>
  );
};
