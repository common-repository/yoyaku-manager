import dt from "@/utils/datetime";
import { __, sprintf } from "@wordpress/i18n";

export const FormHeader = ({ order }) => {
  return (
    <div className="my-3">
      <span className={order === 1 ? "text-primary" : ""}>
        1. {__("Select Date And Time", "yoyaku-manager")}
      </span>
      <span className="mx-2"></span>
      <span className={order === 2 ? "text-primary" : ""}>
        2. {__("Enter Customer Information and Tickets", "yoyaku-manager")}
      </span>
    </div>
  );
};

export const BookingDatetimeLabel = ({ startDatetime }) => (
  <p>
    {sprintf(
      /* translators: %s is datetime */
      __("Booking date and time %s", "yoyaku-manager"),
      dt.getWpFormattedDateTimeString(startDatetime),
    )}
  </p>
);
