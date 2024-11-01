import { __ } from "@wordpress/i18n";
import { Col, Form, Row } from "react-bootstrap";
import { useForm } from "react-hook-form";
import { FieldErrorMessage, SaveBtn } from "@/components/common";
import { BasePage } from "@/components/layout";
import { optionFieldStatus } from "@/utils/consts";
import label from "@/utils/labels";
import { defaultCountryCodeOptions } from "@/utils/validators/settings";

export const General = ({ values, onSubmit }) => {
  const {
    register,
    watch,
    setValue,
    handleSubmit,
    formState: { errors },
  } = useForm({ values });

  return (
    <BasePage>
      <div>
        <h2 className="mb-3">{__("Input Fields", "yoyaku-manager")}</h2>
        <p className="mb-3">
          {__(
            "You can customize the fields your customers enter.Hidden fields will not be displayed on the Administration Screens.",
            "yoyaku-manager",
          )}
        </p>

        <Form noValidate onSubmit={handleSubmit(onSubmit)}>
          {[
            "phone_field_status",
            "ruby_field_status",
            "birthday_field_status",
            "gender_field_status",
            "zipcode_field_status",
            "address_field_status",
          ].map((name) => (
            <Form.Group key={name} as={Row} className="form-group">
              <Form.Label column sm={3}>
                {getOptionStatusLabel(name)}
              </Form.Label>
              <Col sm={9} className="form-checkbox-box">
                {Object.values(optionFieldStatus).map((field_status) => (
                  <Form.Check
                    inline
                    key={`${name}-${field_status}`}
                    type="radio"
                    name={name}
                    label={label.getOptionFieldStatusLabel(field_status)}
                    value={field_status}
                    checked={watch(name) === field_status}
                    onChange={(event) =>
                      setValue(event.target.name, event.target.value)
                    }
                  />
                ))}
              </Col>
            </Form.Group>
          ))}

          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={3}>
              {__("Default Country Code", "yoyaku-manager")}
            </Form.Label>
            <Col sm={6}>
              <Form.Control
                {...register("default_country_code", defaultCountryCodeOptions)}
              />
              {errors.default_country_code && (
                <FieldErrorMessage
                  message={errors.default_country_code.message}
                />
              )}
            </Col>
            <Row>
              <Col sm={3}></Col>
              <Col>
                {__(
                  "If you use international phone number, enter two-letter ISO country code(like US, JP), otherwise blank.",
                  "yoyaku-manager",
                )}
              </Col>
            </Row>
          </Form.Group>

          <hr />
          <h2 className="mb-3">{__("URL Settings", "yoyaku-manager")}</h2>

          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={3}>
              {__("Terms Of Service URL", "yoyaku-manager")}
            </Form.Label>
            <Col sm={6}>
              <Form.Control {...register("terms_of_service_url")} />
            </Col>
          </Form.Group>

          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={3}>
              {__("Booking Cancel URL", "yoyaku-manager")}
            </Form.Label>
            <Col sm={6}>
              <Form.Control {...register("cancel_url")} />
            </Col>
          </Form.Group>

          <hr />
          <h2 className="mb-3">{__("Others", "yoyaku-manager")}</h2>

          <Form.Check
            className="form-group"
            label={__(
              "Delete Content When Delete This Plugin",
              "yoyaku-manager",
            )}
            {...register("delete_content")}
          />

          <SaveBtn />
        </Form>
      </div>
    </BasePage>
  );
};

const getOptionStatusLabel = (status) => {
  switch (status) {
    case "phone_field_status":
      return __("Phone", "yoyaku-manager");

    case "ruby_field_status":
      return __("Ruby", "yoyaku-manager");

    case "birthday_field_status":
      return __("Birthday", "yoyaku-manager");

    case "gender_field_status":
      return __("Gender", "yoyaku-manager");

    case "zipcode_field_status":
      return __("Zipcode", "yoyaku-manager");

    case "address_field_status":
      return __("Address", "yoyaku-manager");
  }
};

export const getGeneralSetting = (settings) => {
  return {
    default_country_code: settings.default_country_code,
    terms_of_service_url: settings.terms_of_service_url,
    cancel_url: settings.cancel_url,
    phone_field_status: settings.phone_field_status,
    ruby_field_status: settings.ruby_field_status,
    birthday_field_status: settings.birthday_field_status,
    zipcode_field_status: settings.zipcode_field_status,
    address_field_status: settings.address_field_status,
    gender_field_status: settings.gender_field_status,
    delete_content: settings.delete_content,
  };
};
