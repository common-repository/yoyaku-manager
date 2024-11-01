import {
  updateEventBookingAPI,
  useEventBooking,
  useTicketsWithSoldCount,
} from "@/api";
import { BookingForm } from "@/components/bookings";
import { LoadingData } from "@/components/common";
import { BaseLicensePage, Header } from "@/components/layout";
import { HandleError } from "@/pages/others";
import { __ } from "@wordpress/i18n";
import { useParams, useSearchParams } from "react-router-dom";

export const UpdateBooking = () => {
  const [searchParams, setSearchParams] = useSearchParams();
  const params = useParams();
  const { data, error, isLoading } = useEventBooking(params.id);
  const navigateTo =
    searchParams.get("from") === "detail" ? `/${params.id}` : "/";
  const {
    data: tickets,
    error: errorTickets,
    isLoading: isLoadingTickets,
  } = useTicketsWithSoldCount({
    event_booking_id: params.id,
    with_sold_count: true,
  });

  const getDefaultValues = (ticketsItems) => {
    let defaultTickets = ticketsItems.map((ticket) => {
      const buyTicket = data.tickets.find((item) => item.id === ticket.id);
      const buy_count = buyTicket ? buyTicket.count : 0;
      return { id: ticket.id, buy_count: buy_count };
    });
    return { ...data, tickets: defaultTickets };
  };

  if (error || errorTickets) {
    return <HandleError error={error || errorTickets} />;
  }
  if (isLoading || isLoadingTickets) return <LoadingData />;

  return (
    <BaseLicensePage>
      <Header title={__("Edit Booking", "yoyaku-manager")} />
      {tickets?.items && (
        <BookingForm
          defaultValues={getDefaultValues(tickets.items)}
          tickets={tickets.items}
          dataHandler={updateEventBookingAPI}
          navigateTo={navigateTo}
        />
      )}
    </BaseLicensePage>
  );
};
