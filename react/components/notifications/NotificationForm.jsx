import {
  APIErrorMessage,
  FieldErrorMessage,
  RequiredLabel,
  SaveBtn,
  showSuccessToast,
} from "@/components/common";
import { notificationTiming } from "@/utils/consts";
import label from "@/utils/labels";
import * as validate from "@/utils/validators/notification";
import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Col, Form, Row } from "react-bootstrap";
import { useForm } from "react-hook-form";
import { useNavigate } from "react-router-dom";
import InlinePlaceholders from "./InlinePlaceholders";

/**
 * @param defaultValues
 * @param dataHandler
 * @returns {JSX.Element|null}
 * @constructor
 */
export const NotificationForm = ({ defaultValues, dataHandler }) => {
  const navigate = useNavigate();
  const [errorResponse, setErrorResponse] = useState();
  const {
    register,
    handleSubmit,
    watch,
    setValue,
    formState: { errors, isSubmitting },
  } = useForm({ values: defaultValues });

  const onSubmit = async (data) => {
    const result = await dataHandler(data);
    if (result?.is_error) {
      setErrorResponse(result);
    } else {
      showSuccessToast();
      navigate("/");
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
            <Form.Label>{__("Timing", "yoyaku-manager")}</Form.Label>
            <Form.Select
              {...register("timing")}
              onChange={(e) => {
                setValue("timing", e.target.value);
                if (e.target.value !== notificationTiming.scheduled) {
                  setValue("days", 0);
                  setValue("time", "00:00:00");
                }
              }}
            >
              <option value={notificationTiming.approved}>
                {label.getNotificationTimingLabel(notificationTiming.approved)}
              </option>
              <option value={notificationTiming.pending}>
                {label.getNotificationTimingLabel(notificationTiming.pending)}
              </option>
              <option value={notificationTiming.canceled}>
                {label.getNotificationTimingLabel(notificationTiming.canceled)}
              </option>
              <option value={notificationTiming.disapproved}>
                {label.getNotificationTimingLabel(
                  notificationTiming.disapproved,
                )}
              </option>
              <option value={notificationTiming.scheduled}>
                {label.getNotificationTimingLabel(notificationTiming.scheduled)}
              </option>
              {errors.timing && (
                <FieldErrorMessage message={errors.timing.message} />
              )}
            </Form.Select>
          </Col>
        </Row>

        {watch("timing") === notificationTiming.scheduled && (
          <Row className="form-group">
            <Col sm>
              <Form.Label>
                {__("Before Or After Event", "yoyaku-manager")}
              </Form.Label>
              <Form.Select {...register("is_before", validate.isBeforeOptions)}>
                <option value="true">{__("Before", "yoyaku-manager")}</option>
                <option value="false">{__("After", "yoyaku-manager")}</option>
              </Form.Select>
              {errors.is_before && (
                <FieldErrorMessage message={errors.is_before.message} />
              )}
            </Col>
            <Col sm>
              <Form.Label>{__("Days", "yoyaku-manager")}</Form.Label>
              <Form.Control
                type="number"
                min={0}
                {...register("days", validate.daysOptions)}
              />
              {errors.days && (
                <FieldErrorMessage message={errors.days.message} />
              )}
            </Col>
            <Col sm>
              <Form.Label>{__("Time", "yoyaku-manager")}</Form.Label>
              {/* setValueAsで、 HH:mm形式 を HH:mm:ss形式に変換する */}
              <Form.Control
                type="time"
                {...register("time", validate.timeOptions)}
              />
              {errors.time && (
                <FieldErrorMessage message={errors.time.message} />
              )}
            </Col>
          </Row>
        )}

        <Form.Group className="form-group">
          <Form.Label>
            {__("Subject", "yoyaku-manager")}
            <RequiredLabel />
          </Form.Label>
          <Form.Control {...register("subject", validate.subjectOptions)} />
          {errors.subject && (
            <FieldErrorMessage message={errors.subject.message} />
          )}
        </Form.Group>

        <Form.Group className="form-group">
          <Form.Label>{__("Message", "yoyaku-manager")}</Form.Label>
          <Form.Control as="textarea" rows={5} {...register("content")} />
          {errors.content && (
            <FieldErrorMessage message={errors.content.message} />
          )}
        </Form.Group>

        <SaveBtn isSubmitting={isSubmitting} />
      </Form>

      <InlinePlaceholders notification={defaultValues} />
    </div>
  );
};
