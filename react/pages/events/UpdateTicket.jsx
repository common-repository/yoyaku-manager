import { __ } from "@wordpress/i18n";
import { useParams } from "react-router-dom";
import { updateTicketAPI, useTicket } from "@/api";
import { LoadingData } from "@/components/common";
import { TicketForm } from "@/components/events";
import { BaseLicensePage, Header } from "@/components/layout";
import { settings } from "@/utils/settings";
import { Forbidden, HandleError } from "@/pages/others";

export const UpdateTicket = () => {
  const params = useParams();
  const { data, error, isLoading } = useTicket(params.ticketId);

  if (!settings.canWrite()) return <Forbidden />;
  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <Header title={__("Edit Ticket", "yoyaku-manager")} />
      <TicketForm defaultValues={data} dataHandler={updateTicketAPI} />
    </BaseLicensePage>
  );
};
