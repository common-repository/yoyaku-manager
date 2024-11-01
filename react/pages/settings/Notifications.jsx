import { __ } from "@wordpress/i18n";
import { Col, Form, Row } from "react-bootstrap";
import { useForm } from "react-hook-form";
import { FieldErrorMessage, SaveBtn } from "@/components/common";
import { BasePage } from "@/components/layout";
import { senderEmailOptions } from "@/utils/validators/settings";

export const Notifications = ({ values, onSubmit }) => {
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm({ values });

  return (
    <BasePage>
      <div>
        <Form noValidate onSubmit={handleSubmit(onSubmit)}>
          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={3}>
              {__("Sender Email", "yoyaku-manager")}
            </Form.Label>
            <Col sm={6}>
              <Form.Control
                type="email"
                {...register("sender_email", senderEmailOptions)}
              />
              {errors.sender_email && (
                <FieldErrorMessage message={errors.sender_email.message} />
              )}
            </Col>
          </Form.Group>

          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={3}>
              {__("Sender Name", "yoyaku-manager")}
            </Form.Label>
            <Col sm={6}>
              <Form.Control {...register("sender_name")} />
            </Col>
          </Form.Group>

          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={3}>
              {__("Bcc Emails(Comma Separated)", "yoyaku-manager")}
            </Form.Label>
            <Col sm={6}>
              <Form.Control {...register("bcc_email")} />
            </Col>
          </Form.Group>

          <SaveBtn />
        </Form>
      </div>
    </BasePage>
  );
};

export const getNotificationsSetting = (settings) => {
  return {
    sender_name: settings.sender_name,
    sender_email: settings.sender_email,
    bbc_email: settings.bbc_email,
  };
};
