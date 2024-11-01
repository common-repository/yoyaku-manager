import { addEventBookingAPI, useTicketsWithSoldCount } from "@/api";
import { BookingForm } from "@/components/bookings";
import { LoadingData } from "@/components/common";
import { BaseLicensePage, Header } from "@/components/layout";
import { HandleError } from "@/pages/others";
import { __, sprintf } from "@wordpress/i18n";
import { useParams } from "react-router-dom";

export const AddBooking = () => {
  const params = useParams();
  const getDefaultValues = (ticketsItems) => {
    return {
      event_period_id: params.periodId,
      amount: 0,
      tickets: ticketsItems.map((ticket) => {
        return { id: ticket.id, buy_count: 0 };
      }),
    };
  };
  const {
    data: tickets,
    error,
    isLoading,
  } = useTicketsWithSoldCount({
    event_period_id: params.periodId,
    with_sold_count: true,
  });

  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <Header title={__("Add Booking", "yoyaku-manager")} />
      <p>
        {
          /* translators: %d is replaced with "number" */
          sprintf(__("Period ID: %d", "yoyaku-manager"), params.periodId)
        }
      </p>

      {tickets?.items && (
        <BookingForm
          defaultValues={getDefaultValues(tickets.items)}
          tickets={tickets.items}
          dataHandler={addEventBookingAPI}
          navigateTo="/"
        />
      )}
    </BaseLicensePage>
  );
};
