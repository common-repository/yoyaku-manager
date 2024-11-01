import { useNotifications } from "@/api";
import {
  APIErrorMessage,
  FieldErrorMessage,
  RequiredLabel,
  SaveBtn,
  showSuccessToast,
  TimingBadge,
} from "@/components/common";
import * as validate from "@/utils/validators/event";
import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Col, Form, InputGroup, Row } from "react-bootstrap";
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
export const EventForm = ({ defaultValues, dataHandler, navigateTo }) => {
  const navigate = useNavigate();
  const [errorResponse, setErrorResponse] = useState();
  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm({ values: defaultValues });

  const onSubmit = async (data) => {
    let copied = { ...data };
    // 時から分に変換する
    copied.min_time_to_close_booking = copied.min_time_to_close_booking * 60;
    copied.min_time_to_cancel_booking = copied.min_time_to_cancel_booking * 60;

    const result = await dataHandler(copied);
    if (result?.is_error) {
      setErrorResponse(result);
    } else {
      showSuccessToast();
      navigate(navigateTo);
    }
  };
  const { data: notifications, error } = useNotifications({});

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

        {/* apiのパラメーターの単位は分だが、入力は時にしている */}
        <Row>
          <Col sm={6} className="form-group">
            <Form.Label>
              {__("Reservation deadline", "yoyaku-manager")}
            </Form.Label>
            <InputGroup>
              <Form.Control
                type="number"
                min={0}
                {...register(
                  "min_time_to_close_booking",
                  validate.minTimeToCloseBookingOptions,
                )}
              />
              <InputGroup.Text>{__("Hours", "yoyaku-manager")}</InputGroup.Text>
            </InputGroup>
            {errors.min_time_to_close_booking && (
              <FieldErrorMessage
                message={errors.min_time_to_close_booking.message}
              />
            )}
          </Col>
        </Row>

        <Row>
          <Col sm={6} className="form-group">
            <Form.Label>
              {__("Cancellation Reception Deadline", "yoyaku-manager")}
            </Form.Label>
            <InputGroup>
              <Form.Control
                type="number"
                min={0}
                {...register(
                  "min_time_to_cancel_booking",
                  validate.minTimeToCancelBookingOptions,
                )}
              />
              <InputGroup.Text>{__("Hours", "yoyaku-manager")}</InputGroup.Text>
            </InputGroup>
            {errors.min_time_to_cancel_booking && (
              <FieldErrorMessage
                message={errors.min_time_to_cancel_booking.message}
              />
            )}
          </Col>
        </Row>

        <Row>
          <Col sm={6} className="form-group">
            <Form.Label>
              {__("Max Tickets Per Booking", "yoyaku-manager")}
            </Form.Label>
            <Form.Control
              type="number"
              min={1}
              {...register(
                "max_tickets_per_booking",
                validate.maxTicketsPerBookingOption,
              )}
            />
            {errors.max_tickets_per_booking && (
              <FieldErrorMessage
                message={errors.max_tickets_per_booking.message}
              />
            )}
          </Col>
        </Row>

        <Form.Check
          className="form-group"
          label={__("Show Organizer", "yoyaku-manager")}
          {...register("show_worker")}
        />

        <Form.Check
          className="form-group"
          label={__("Use Approval System", "yoyaku-manager")}
          {...register("use_approval_system")}
        />

        <Form.Group className="form-group">
          <Form.Label>{__("Redirect URL", "yoyaku-manager")}</Form.Label>
          <Form.Control
            {...register("redirect_url", validate.redirectUrlOptions)}
          />
          {errors.redirect_url && (
            <FieldErrorMessage message={errors.redirect_url.message} />
          )}
        </Form.Group>

        <Form.Group className="form-group">
          <Form.Label>{__("Description", "yoyaku-manager")}</Form.Label>
          <Form.Control
            as="textarea"
            rows={3}
            {...register("description", validate.descriptionOptions)}
          />
          {errors.description && (
            <FieldErrorMessage message={errors.description.message} />
          )}
        </Form.Group>

        {notifications?.items && (
          <Form.Group className="form-group">
            <Form.Label>{__("Notification", "yoyaku-manager")}</Form.Label>
            {notifications.items.map((item) => (
              <Form.Check
                key={item.id}
                className="form-group"
                value={item.id}
                defaultChecked={defaultValues.notification_ids.includes(
                  item.id,
                )}
                label={<TimingBadge timing={item.timing} name={item.name} />}
                {...register("notification_ids")}
              />
            ))}
          </Form.Group>
        )}

        <SaveBtn isSubmitting={isSubmitting} />
      </Form>
    </div>
  );
};
