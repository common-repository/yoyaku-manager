import { FieldErrorMessage } from "@/components/common";
import { getFormattedPrice, getTotalPrice } from "@/utils/price";
import { generateTicketsOptions } from "@/utils/validators/event";
import { __ } from "@wordpress/i18n";
import { Col, Form, Row } from "react-bootstrap";

/**
 * チケットの種類選択や、購入数を入力できるフォーム
 * @param buyTickets {array} チケットのデータ
 * @param fields {object} useFieldArray() で定義したチケットのfields
 * @param errors
 * @param register
 * @param maxTotalTickets チケットの最大購入可能枚数
 * @param useFixedTicket
 * @returns {JSX.Element|null}
 * @constructor
 */
export const TicketsForm = ({
  buyTickets,
  fields,
  errors,
  register,
  maxTotalTickets,
  useFixedTicket,
}) => {
  const message = useFixedTicket
    ? __("Tickets (Limited to 1 ticket per person.)", "yoyaku-manager")
    : sprintf(
        /* translators: %d is max_tickets_per_booking */
        __("Select a ticket. Limit %d per person.", "yoyaku-manager"),
        maxTotalTickets,
      );

  return (
    <>
      <p>{message}</p>
      {fields.map((field, index) => (
        <Row key={field.id} className="form-group mb-4">
          <Form.Label column sm={6}>
            {getLabel(
              buyTickets[index].name,
              buyTickets[index].price,
              buyTickets[index].ticket_count -
                buyTickets[index].sold_ticket_count <=
                0,
            )}
          </Form.Label>
          <Col sm={6}>
            <Form.Control
              type="number"
              min={0}
              disabled={
                useFixedTicket ||
                buyTickets[index].ticket_count -
                  buyTickets[index].sold_ticket_count <=
                  0
              }
              {...register(
                `tickets.${index}.buy_count`,
                generateTicketsOptions(maxTotalTickets),
              )}
            />
          </Col>
        </Row>
      ))}
      {errors.tickets?.[0]?.buy_count && (
        <FieldErrorMessage message={errors.tickets[0].buy_count.message} />
      )}

      <p>
        <b>{__("Payment Amount", "yoyaku-manager")}</b>
        <span className="ms-2">
          <b>{getFormattedPrice(getTotalPrice(buyTickets))}</b>
        </span>
      </p>
    </>
  );
};

const getLabel = (name, price, isSoldOut) => (
  <>
    {`${name} ${getFormattedPrice(price)}`}
    {isSoldOut && (
      <span className="ms-2 text-danger">
        {__("(Sold Out)", "yoyaku-manager")}
      </span>
    )}
  </>
);
