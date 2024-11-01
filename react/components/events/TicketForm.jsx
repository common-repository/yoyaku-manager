import {
  APIErrorMessage,
  FieldErrorMessage,
  RequiredLabel,
  SaveBtn,
  showSuccessToast,
} from "@/components/common";
import { settings } from "@/utils/settings";
import * as validate from "@/utils/validators/event";
import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Col, Form, Row } from "react-bootstrap";
import { useForm } from "react-hook-form";
import { useNavigate } from "react-router-dom";

/**
 *
 * @param defaultValues
 * @param dataHandler
 * @returns {JSX.Element}
 * @constructor
 */
export const TicketForm = ({ defaultValues, dataHandler }) => {
  const navigate = useNavigate();
  const [errorResponse, setErrorResponse] = useState();
  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm({ values: defaultValues });

  const onSubmit = async (data) => {
    const result = await dataHandler(data);
    if (result?.is_error) {
      setErrorResponse(result);
    } else {
      showSuccessToast();
      navigate(`/${data.event_id}`);
    }
  };

  return (
    <div className="y-form-wrap">
      {errorResponse && <APIErrorMessage errorResponse={errorResponse} />}

      <Form noValidate onSubmit={handleSubmit(onSubmit)}>
        <Form.Group className="form-group">
          <Form.Label>
            {__("Name", "yoyaku-manager")}
            <RequiredLabel />
          </Form.Label>
          <Form.Control {...register("name", validate.nameOptions)} />
          {errors.name && <FieldErrorMessage message={errors.name.message} />}
        </Form.Group>

        <Row>
          <Col sm={6} className="form-group">
            <Form.Label>{__("Price", "yoyaku-manager")}</Form.Label>
            <Form.Control
              type="number"
              min={0}
              step={1 ** (-1 * settings.price_decimals)}
              className="y-regular-text"
              {...register("price", validate.priceOptions)}
            />
            {errors.price && (
              <FieldErrorMessage message={errors.price.message} />
            )}
          </Col>
          <Col sm={6} className="form-group">
            <Form.Label>{__("Ticket Count", "yoyaku-manager")}</Form.Label>
            <Form.Control
              type="number"
              min={0}
              className="y-regular-text"
              {...register("ticket_count", validate.ticketCountOptions)}
            />
            {errors.ticket_count && (
              <FieldErrorMessage message={errors.ticket_count.message} />
            )}
          </Col>
        </Row>

        <SaveBtn isSubmitting={isSubmitting} />
      </Form>
    </div>
  );
};
