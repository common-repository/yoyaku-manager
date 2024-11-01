import { __ } from "@wordpress/i18n";
import { useNavigate, useSearchParams } from "react-router-dom";
import { addTicketAPI } from "@/api";
import { TicketForm } from "@/components/events";
import { BaseLicensePage, Header } from "@/components/layout";
import { settings } from "@/utils/settings";
import { Forbidden, HandleError } from "@/pages/others";

export const AddTicket = () => {
  const navigate = useNavigate();
  const [searchParams, setSearchParams] = useSearchParams();
  const defaultValues = {
    event_id: parseInt(searchParams.get("eventId")),
    name: "",
    price: 0,
    ticket_count: 0,
  };

  if (!searchParams.has("eventId")) {
    return <HandleError error={{ message: "Invalid event ID" }} />;
  }
  if (!settings.canWrite()) return <Forbidden />;

  return (
    <BaseLicensePage>
      <Header title={__("Add Ticket", "yoyaku-manager")} />
      <TicketForm defaultValues={defaultValues} dataHandler={addTicketAPI} />
    </BaseLicensePage>
  );
};
