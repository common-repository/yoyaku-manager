import { deleteEventBookingAPI, useEventBooking } from "@/api";
import { UpdateBookingStatusConfirmBtn } from "@/components/bookings";
import {
  ActionDivider,
  BookingStatusBadge,
  BtnGroup,
  CardRow,
  DeleteConfirmLink,
  EditLink,
  LoadingData,
  PaymentStatusBadge,
  TableActionGroup,
} from "@/components/common";
import { BaseLicensePage, Header } from "@/components/layout";
import { HandleError } from "@/pages/others";
import { bookingStatus, gatewayType } from "@/utils/consts";
import dt from "@/utils/datetime";
import label from "@/utils/labels";
import { getFormattedPrice } from "@/utils/price";
import { settings } from "@/utils/settings";
import { __ } from "@wordpress/i18n";
import { Card, Row, Table } from "react-bootstrap";
import { useParams } from "react-router-dom";

export const BookingDetail = () => {
  const optionFields = settings.getOptionFieldSettings();
  const params = useParams();
  const { data, error, isLoading, mutate } = useEventBooking(params.id);
  const mutateFn = () => mutate(data);

  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <Header title={__("Booking Detail", "yoyaku-manager")} />

      {settings.canWrite() &&
        ![bookingStatus.canceled, bookingStatus.disapproved].includes(
          data.status,
        ) && (
          <Row className="detail-card-wrap">
            <Card>
              <Card.Body>
                {data.status === bookingStatus.approved && (
                  <>
                    <Card.Text>
                      {__("This booking has been approved.", "yoyaku-manager")}
                    </Card.Text>
                    <BtnGroup>
                      <UpdateBookingStatusConfirmBtn
                        id={data.id}
                        status={bookingStatus.canceled}
                        mutateFn={mutateFn}
                      />
                    </BtnGroup>
                  </>
                )}
                {data.status === bookingStatus.pending && (
                  <>
                    <Card.Text>
                      {__("This booking is pending.", "yoyaku-manager")}
                    </Card.Text>
                    <BtnGroup className="mt-3">
                      <UpdateBookingStatusConfirmBtn
                        id={data.id}
                        status={bookingStatus.approved}
                        mutateFn={mutateFn}
                      />
                      <UpdateBookingStatusConfirmBtn
                        id={data.id}
                        status={bookingStatus.disapproved}
                        mutateFn={mutateFn}
                      />
                    </BtnGroup>
                  </>
                )}
              </Card.Body>
            </Card>
          </Row>
        )}

      <Row className="detail-card-wrap">
        <Card>
          <Card.Body>
            <CardRow header={__("Status", "yoyaku-manager")}>
              <BookingStatusBadge status={data.status} />
              <span className="ms-2">
                <PaymentStatusBadge status={data.payment_status} />
              </span>
            </CardRow>

            <CardRow header={__("Event Name", "yoyaku-manager")}>
              {data.event_name}
            </CardRow>

            <CardRow header={__("Start DateTime", "yoyaku-manager")}>
              {dt.getWpFormattedDateTimeString(data.period_start_datetime)}
            </CardRow>

            <CardRow header={__("Name", "yoyaku-manager")}>
              {`${data.first_name} ${data.last_name}`}
            </CardRow>

            {!optionFields.rubyIsHidden && (
              <CardRow header={__("Ruby", "yoyaku-manager")}>
                {`${data.first_name_ruby} ${data.last_name_ruby}`}
              </CardRow>
            )}

            <CardRow header={__("Email", "yoyaku-manager")}>
              {data.email}
            </CardRow>

            {!optionFields.phoneIsHidden && (
              <CardRow header={__("Phone", "yoyaku-manager")}>
                {data.phone}
              </CardRow>
            )}

            {!optionFields.genderIsHidden && (
              <CardRow header={__("Gender", "yoyaku-manager")}>
                {label.getGenderLabel(data.gender)}
              </CardRow>
            )}

            {!optionFields.zipcodeIsHidden && (
              <CardRow header={__("Zipcode", "yoyaku-manager")}>
                {data.zipcode}
              </CardRow>
            )}

            {!optionFields.addressIsHidden && (
              <CardRow header={__("Address", "yoyaku-manager")}>
                {data.address}
              </CardRow>
            )}

            {!optionFields.birthdayIsHidden && (
              <CardRow header={__("Birthday", "yoyaku-manager")}>
                {data.birthday}
              </CardRow>
            )}

            <CardRow header={__("Memo", "yoyaku-manager")}>
              <div style={{ whiteSpace: "pre-line" }}>{data.memo}</div>
            </CardRow>

            <CardRow header={__("Payment Amount", "yoyaku-manager")}>
              {getFormattedPrice(data.amount)}
            </CardRow>

            <CardRow header={__("Gateway", "yoyaku-manager")}>
              {label.getGatewayLabel(data.gateway)}
            </CardRow>

            {data.gateway !== gatewayType.on_site && (
              <CardRow header={__("Transaction ID", "yoyaku-manager")}>
                {data.transaction_id}
              </CardRow>
            )}

            <CardRow header={__("Created", "yoyaku-manager")}>
              {dt.getWpFormattedDateTimeString(data.created)}
            </CardRow>

            <TableActionGroup isVisible>
              <EditLink id={data.id} from="detail" />
              {settings.canWrite() && settings.canDelete() && <ActionDivider />}
              <DeleteConfirmLink
                id={data.id}
                deleteAPI={deleteEventBookingAPI}
                navigateTo="/"
              />
            </TableActionGroup>
          </Card.Body>
        </Card>
      </Row>

      <TicketTable tickets={data.tickets} />
    </BaseLicensePage>
  );
};

const TicketTable = ({ tickets }) => {
  return (
    <div className="mt-2">
      <h2>{__("Bought Tickets", "yoyaku-manager")}</h2>
      <Table striped hover>
        <thead>
          <tr>
            <th>{__("Name", "yoyaku-manager")}</th>
            <th>{__("Ticket Count", "yoyaku-manager")}</th>
          </tr>
        </thead>
        <tbody>
          {tickets.map((ticket, index) => (
            <tr key={index}>
              <td>{ticket.name} </td>
              <td>{ticket.count} </td>
            </tr>
          ))}
        </tbody>
      </Table>
    </div>
  );
};
