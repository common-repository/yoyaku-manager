import {
  APIErrorMessage,
  FieldErrorMessage,
  RequiredLabel,
  SaveBtn,
  showSuccessToast,
} from "@/components/common";
import { gender } from "@/utils/consts";
import label from "@/utils/labels";
import { settings } from "@/utils/settings";
import * as validate from "@/utils/validators/customer";
import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Col, Form, Row } from "react-bootstrap";
import { useForm } from "react-hook-form";
import PhoneInput from "react-phone-number-input";
import { useNavigate } from "react-router-dom";
import "react-phone-number-input/style.css";

/**
 *
 * @param defaultValues
 * @param dataHandler
 * @param navigateTo
 * @returns {JSX.Element}
 * @constructor
 */
export const CustomerForm = ({ defaultValues, dataHandler, navigateTo }) => {
  const optionFields = settings.getOptionFieldSettings();
  const navigate = useNavigate();
  const [errorResponse, setErrorResponse] = useState();
  const {
    register,
    watch,
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
              {__("First Name", "yoyaku-manager")}
              <RequiredLabel />
            </Form.Label>
            <Form.Control {...register("first_name", validate.nameOptions)} />
            {errors.first_name && (
              <FieldErrorMessage message={errors.first_name.message} />
            )}
          </Form.Group>
          <Form.Group as={Col}>
            <Form.Label>
              {__("Last Name", "yoyaku-manager")}
              <RequiredLabel />
            </Form.Label>
            <Form.Control {...register("last_name", validate.nameOptions)} />
            {errors.last_name && (
              <FieldErrorMessage message={errors.last_name.message} />
            )}
          </Form.Group>
        </Row>
        {!optionFields.rubyIsHidden && (
          <Row className="mb-3">
            <Form.Group as={Col}>
              <Form.Label>{__("First Name Ruby", "yoyaku-manager")}</Form.Label>
              <Form.Control
                {...register("first_name_ruby", validate.rubyOptions(false))}
              />
              {errors.first_name_ruby && (
                <FieldErrorMessage message={errors.first_name_ruby.message} />
              )}
            </Form.Group>
            <Form.Group as={Col}>
              <Form.Label>{__("Last Name Ruby", "yoyaku-manager")}</Form.Label>
              <Form.Control
                {...register("last_name_ruby", validate.rubyOptions(false))}
              />
              {errors.last_name_ruby && (
                <FieldErrorMessage message={errors.last_name_ruby.message} />
              )}
            </Form.Group>
          </Row>
        )}

        <Form.Group className="form-group">
          <Form.Label>
            {__("Email", "yoyaku-manager")}
            <RequiredLabel />
          </Form.Label>
          <Form.Control
            type="email"
            {...register("email", validate.emailOptions)}
          />
          {errors.email && <FieldErrorMessage message={errors.email.message} />}
        </Form.Group>

        {!optionFields.phoneIsHidden && (
          <Row>
            <Col sm={6} className="form-group">
              <Form.Label>{__("Phone", "yoyaku-manager")}</Form.Label>
              {settings.default_country_code ? (
                <PhoneInput
                  defaultCountry={settings.default_country_code}
                  value={watch("phone", "")}
                  onChange={(value) => setValue("phone", value)}
                />
              ) : (
                <Form.Control
                  {...register("phone", validate.phoneOptions(false))}
                />
              )}
              {errors.phone && (
                <FieldErrorMessage message={errors.phone.message} />
              )}
            </Col>
          </Row>
        )}

        {!optionFields.zipcodeIsHidden && (
          <Row>
            <Col sm={6} className="form-group">
              <Form.Label>{__("Zipcode", "yoyaku-manager")}</Form.Label>
              <Form.Control
                {...register("zipcode", validate.zipcodeOptions(false))}
              />
              {errors.zipcode && (
                <FieldErrorMessage message={errors.zipcode.message} />
              )}
            </Col>
          </Row>
        )}

        {!optionFields.addressIsHidden && (
          <Form.Group className="form-group">
            <Form.Label>{__("Address", "yoyaku-manager")}</Form.Label>
            <Form.Control
              {...register("address", validate.addressOptions(false))}
            />
            {errors.address && (
              <FieldErrorMessage message={errors.address.message} />
            )}
          </Form.Group>
        )}

        {/* 性別と誕生日のoptionFields設定に応じて最大２つのフォームを並べる */}
        {!optionFields.birthdayIsHidden && (
          <Row>
            <Col sm={6} className="form-group">
              <Form.Label>{__("Birthday", "yoyaku-manager")}</Form.Label>
              <Form.Control
                type="date"
                {...register("birthday", validate.birthdayOptions(false))}
              />
              {errors.birthday && (
                <FieldErrorMessage message={errors.birthday.message} />
              )}
            </Col>
          </Row>
        )}

        {!optionFields.genderIsHidden && (
          <Row>
            <Col sm={6} className="form-group">
              <Form.Label>{__("Gender", "yoyaku-manager")}</Form.Label>
              <Form.Select {...register("gender")}>
                <option value={gender.unknown}></option>
                <option value={gender.male}>
                  {label.getGenderLabel(gender.male)}
                </option>
                <option value={gender.female}>
                  {label.getGenderLabel(gender.female)}
                </option>
              </Form.Select>
              {errors.gender && (
                <FieldErrorMessage message={errors.gender.message} />
              )}
            </Col>
          </Row>
        )}

        <Form.Group className="form-group">
          <Form.Label>{__("Memo", "yoyaku-manager")}</Form.Label>
          <Form.Control as="textarea" rows={3} {...register("memo")} />
          {errors.memo && <FieldErrorMessage message={errors.memo.message} />}
        </Form.Group>

        <SaveBtn isSubmitting={isSubmitting} />
      </Form>
    </div>
  );
};
