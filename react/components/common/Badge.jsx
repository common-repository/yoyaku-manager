import {
  bookingStatus,
  notificationTiming,
  paymentStatus,
} from "@/utils/consts";
import label from "@/utils/labels";
import { Badge } from "react-bootstrap";
import { Clock } from "react-bootstrap-icons";

export const TimingBadge = ({ timing, name }) => {
  let badge;
  switch (timing) {
    case notificationTiming.approved:
      badge = (
        <Badge bg="success">
          {label.getNotificationTimingLabel(notificationTiming.approved)}
        </Badge>
      );
      break;
    case notificationTiming.pending:
      badge = (
        <Badge bg="secondary">
          {label.getNotificationTimingLabel(notificationTiming.pending)}
        </Badge>
      );
      break;
    case notificationTiming.canceled:
      badge = (
        <Badge bg="warning" text="dark">
          {label.getNotificationTimingLabel(notificationTiming.canceled)}
        </Badge>
      );
      break;
    case notificationTiming.disapproved:
      badge = (
        <Badge bg="danger">
          {label.getNotificationTimingLabel(notificationTiming.disapproved)}
        </Badge>
      );
      break;
    case notificationTiming.scheduled:
      badge = <Clock size={20} color="#29528A" />;
      break;
  }

  return (
    <>
      {badge}
      <span className={"ps-2"}>{name}</span>
    </>
  );
};

export const BookingStatusBadge = ({ status }) => {
  switch (status) {
    case bookingStatus.approved:
      return (
        <Badge bg="success">
          {label.getBookingStatusLabel(bookingStatus.approved)}
        </Badge>
      );

    case bookingStatus.pending:
      return (
        <Badge bg="secondary">
          {label.getBookingStatusLabel(bookingStatus.pending)}
        </Badge>
      );

    case bookingStatus.canceled:
      return (
        <Badge bg="warning" text="dark">
          {label.getBookingStatusLabel(bookingStatus.canceled)}
        </Badge>
      );

    case bookingStatus.disapproved:
      return (
        <Badge bg="danger">
          {label.getBookingStatusLabel(bookingStatus.disapproved)}
        </Badge>
      );
  }
};

/**
 *
 * @param status
 * @returns {JSX.Element}
 * @constructor
 */
export const PaymentStatusBadge = ({ status }) => {
  let badge;

  switch (status) {
    case paymentStatus.paid:
      badge = (
        <Badge pill bg="success">
          {label.getPaymentStatusLabel(paymentStatus.paid)}
        </Badge>
      );
      break;
    case paymentStatus.pending:
      badge = (
        <Badge pill bg="warning" text="dark">
          {label.getPaymentStatusLabel(paymentStatus.pending)}
        </Badge>
      );
      break;
    case paymentStatus.refunded:
      badge = (
        <Badge pill bg="secondary" text="dark">
          {label.getPaymentStatusLabel(paymentStatus.refunded)}
        </Badge>
      );
      break;
  }

  return <>{badge}</>;
};
