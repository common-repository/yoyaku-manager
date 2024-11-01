import {
  APIErrorMessage,
  FieldErrorMessage,
  RequiredLabel,
  SaveBtn,
  showSuccessToast,
} from "@/components/common";
import { SelectOrganizerField } from "@/components/workers";
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
 * @param navigateTo
 * @returns {JSX.Element}
 * @constructor
 */
export const PeriodForm = ({ defaultValues, dataHandler, navigateTo }) => {
  const navigate = useNavigate();
  const [errorResponse, setErrorResponse] = useState();
  const {
    register,
    handleSubmit,
    setValue,
    formState: { errors, isSubmitting },
  } = useForm({ values: defaultValues });

  const onSubmit = async (data) => {
    const result = await dataHandler(data);
    if (result?.is_error) {
      setErrorResponse(result);
    } else {
      showSuccessToast();
      navigate(navigateTo);
    }
  };

  return (
    <div className="y-form-wrap">
      {errorResponse && <APIErrorMessage errorResponse={errorResponse} />}

      <Form noValidate onSubmit={handleSubmit(onSubmit)}>
        <Row className="mb-3">
          <Form.Group as={Col}>
            <Form.Label>
              {__("Start DateTime", "yoyaku-manager")}
              <RequiredLabel />
            </Form.Label>
            <Form.Control
              type="datetime-local"
              {...register("start_datetime", validate.startDatetimeOptions)}
            />
            {errors.start_datetime && (
              <FieldErrorMessage message={errors.start_datetime.message} />
            )}
          </Form.Group>

          <Form.Group as={Col}>
            <Form.Label>
              {__("End DateTime", "yoyaku-manager")}
              <RequiredLabel />
            </Form.Label>
            <Form.Control
              type="datetime-local"
              {...register("end_datetime", validate.datetimeOptions)}
            />
            {errors.end_datetime && (
              <FieldErrorMessage message={errors.end_datetime.message} />
            )}
          </Form.Group>
        </Row>

        <Row>
          <Col sm={6} className="form-group">
            <Form.Label>
              {__("Max Ticket Count", "yoyaku-manager")}
              <RequiredLabel />
            </Form.Label>
            <Form.Control
              type="number"
              min={1}
              {...register("max_ticket_count", validate.maxTicketCountOptions)}
            />
            {errors.max_ticket_count && (
              <FieldErrorMessage message={errors.max_ticket_count.message} />
            )}
          </Col>
        </Row>

        <SelectOrganizerField wpId={defaultValues.wp_id} setValue={setValue} />

        <Form.Group className="form-group">
          <Form.Label>{__("Location", "yoyaku-manager")}</Form.Label>
          <Form.Control {...register("location", validate.locationOptions)} />
          {errors.location && (
            <FieldErrorMessage message={errors.location.message} />
          )}
        </Form.Group>

        <Form.Group className="form-group">
          <Form.Label>{__("Online Meeting URL", "yoyaku-manager")}</Form.Label>
          <Form.Control {...register("online_meeting_url")} />
          {errors.online_meeting_url && (
            <FieldErrorMessage message={errors.online_meeting_url.message} />
          )}
        </Form.Group>

        <SaveBtn isSubmitting={isSubmitting} />
      </Form>
    </div>
  );
};
