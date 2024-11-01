import { useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { Card, Row, Table } from "react-bootstrap";
import { useParams } from "react-router-dom";
import { deleteEventAPI, deleteTicketAPI, useEvent } from "@/api";
import {
  ActionDivider,
  CardRow,
  DeleteConfirmLink,
  EditLink,
  LoadingData,
  TableActionGroup,
  TimingBadge,
} from "@/components/common";
import { EventBookingListBtn, PeriodTable } from "@/components/events";
import { BaseLicensePage, H2WithAddBtn, Header } from "@/components/layout";
import { getFormattedPrice } from "@/utils/price";
import { settings } from "@/utils/settings";
import { HandleError } from "@/pages/others";

export const EventDetail = () => {
  const { id } = useParams();
  const { data: event, error, isLoading } = useEvent(parseInt(id));

  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <Header title={__("Event Detail", "yoyaku-manager")}>
        <EventBookingListBtn eventId={event.id} />
      </Header>

      <Row className="detail-card-wrap">
        <Card>
          <Card.Body>
            <CardRow header="ID">{event.id}</CardRow>

            <CardRow header={__("Name", "yoyaku-manager")}>
              {event.name}
            </CardRow>

            <CardRow
              header={__("Minimum Time To Close Booking", "yoyaku-manager")}
            >
              {sprintf(
                /* translators: %d is replaced with "number" */
                __("Before %d Hours", "yoyaku-manager"),
                Math.trunc(event.min_time_to_close_booking / 60),
              )}
            </CardRow>

            <CardRow
              header={__("Minimum Time To Cancel Booking", "yoyaku-manager")}
            >
              {sprintf(
                /* translators: %d is replaced with "number" */
                __("Before %d Hours", "yoyaku-manager"),
                Math.trunc(event.min_time_to_cancel_booking / 60),
              )}
            </CardRow>

            <CardRow header={__("Max Tickets Per Booking", "yoyaku-manager")}>
              {event.max_tickets_per_booking}
            </CardRow>

            <CardRow header={__("Show Organizer", "yoyaku-manager")}>
              {event.show_worker
                ? __("Show", "yoyaku-manager")
                : __("Not Show", "yoyaku-manager")}
            </CardRow>

            <CardRow header={__("Approval System", "yoyaku-manager")}>
              {event.use_approval_system
                ? __("Use", "yoyaku-manager")
                : __("Not Use", "yoyaku-manager")}
            </CardRow>

            <CardRow header={__("Is Online Payment", "yoyaku-manager")}>
              {event.is_online_payment
                ? __("Yes", "yoyaku-manager")
                : __("No", "yoyaku-manager")}
            </CardRow>

            <CardRow header={__("Redirect URL", "yoyaku-manager")}>
              {event.redirect_url}
            </CardRow>

            <CardRow header={__("Description", "yoyaku-manager")}>
              <div style={{ whiteSpace: "pre-line" }}>{event.description}</div>
            </CardRow>

            <CardRow header={__("Notifications", "yoyaku-manager")}>
              {event.notifications.map((item) => (
                <span className={"pe-3"}>
                  <TimingBadge timing={item.timing} name={item.name} />
                </span>
              ))}
            </CardRow>

            <TableActionGroup isVisible>
              <EditLink id={event.id} from="detail" />
              {settings.canWrite() && settings.canDelete() && <ActionDivider />}
              <DeleteConfirmLink
                id={event.id}
                deleteAPI={deleteEventAPI}
                navigateTo={"/"}
              />
            </TableActionGroup>
          </Card.Body>
        </Card>
      </Row>

      <hr />
      <TicketTable eventId={event.id} tickets={event.tickets} />

      <hr />
      <PeriodTable eventId={event.id} />
    </BaseLicensePage>
  );
};

const TicketTable = ({ tickets, eventId }) => {
  const [selectedId, setSelectedId] = useState();
  return (
    <>
      <H2WithAddBtn
        title={__("Tickets", "yoyaku-manager")}
        to={`/tickets/add?eventId=${eventId}`}
      />
      <Table striped hover>
        <thead>
          <tr>
            <th width="50%">{__("Name", "yoyaku-manager")}</th>
            <th width="30%">{__("Price", "yoyaku-manager")}</th>
            <th>{__("Ticket Count", "yoyaku-manager")}</th>
          </tr>
        </thead>
        <tbody>
          {tickets.map((ticket) => (
            <tr
              key={ticket.id}
              onMouseEnter={() => setSelectedId(ticket.id)}
              onMouseLeave={() => setSelectedId(null)}
            >
              <td>
                {ticket.name}
                <br />
                <TableActionGroup isVisible={selectedId === ticket.id}>
                  <EditLink id={ticket.id} prefix={"/tickets/"} />
                  {settings.canWrite() && settings.canDelete() && (
                    <ActionDivider />
                  )}
                  <DeleteConfirmLink
                    id={ticket.id}
                    deleteAPI={deleteTicketAPI}
                    navigateTo={0} // reload
                  />
                </TableActionGroup>
              </td>
              <td>{getFormattedPrice(ticket.price)}</td>
              <td>{ticket.ticket_count}</td>
            </tr>
          ))}
        </tbody>
      </Table>
    </>
  );
};
