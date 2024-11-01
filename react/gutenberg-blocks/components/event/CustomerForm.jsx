import { FieldErrorMessage, RequiredLabel } from "@/components/common";
import { gender } from "@/utils/consts";
import label from "@/utils/labels";
import { settings } from "@/utils/settings";
import * as validate from "@/utils/validators/customer";
import { __ } from "@wordpress/i18n";
import { Col, Form, Row } from "react-bootstrap";
import PhoneInput from "react-phone-number-input";

/**
 * 顧客データのフォーム
 * @param errors
 * @param register
 * @param setValue
 * @param watchPhone
 * @returns {JSX.Element|null}
 * @constructor
 */
export const CustomerForm = ({ errors, register, setValue, watchPhone }) => {
  const optionFields = settings.getOptionFieldSettings();

  return (
    <>
      <div>
        <Row className="form-group mb-4">
          <Col sm={6}>
            <Form.Label>
              {__("First Name", "yoyaku-manager")}
              <RequiredLabel />
            </Form.Label>
            <Form.Control {...register("first_name", validate.nameOptions)} />
            {errors.first_name && (
              <FieldErrorMessage message={errors.first_name.message} />
            )}
          </Col>

          <Col sm={6}>
            <Form.Label>
              {__("Last Name", "yoyaku-manager")}
              <RequiredLabel />
            </Form.Label>
            <Form.Control {...register("last_name", validate.nameOptions)} />
            {errors.last_name && (
              <FieldErrorMessage message={errors.last_name.message} />
            )}
          </Col>
        </Row>

        {!optionFields.rubyIsHidden && (
          <Row className="form-group mb-4">
            <Col sm={6}>
              <Form.Label>
                {__("First Name Ruby", "yoyaku-manager")}
                {optionFields.rubyIsRequired && <RequiredLabel />}
              </Form.Label>
              <Form.Control
                {...register(
                  "first_name_ruby",
                  validate.rubyOptions(optionFields.rubyIsRequired),
                )}
              />
              {errors.first_name_ruby && (
                <FieldErrorMessage message={errors.first_name_ruby.message} />
              )}
            </Col>

            <Col sm={6}>
              <Form.Label>
                {__("Last Name Ruby", "yoyaku-manager")}
                {optionFields.rubyIsRequired && <RequiredLabel />}
              </Form.Label>
              <Form.Control
                {...register(
                  "last_name_ruby",
                  validate.rubyOptions(optionFields.rubyIsRequired),
                )}
              />
              {errors.last_name_ruby && (
                <FieldErrorMessage message={errors.last_name_ruby.message} />
              )}
            </Col>
          </Row>
        )}

        <Form.Group className="form-group mb-4">
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
          <Form.Group className="form-group mb-4">
            <Form.Label>
              {__("Phone", "yoyaku-manager")}
              {optionFields.phoneIsRequired && <RequiredLabel />}
            </Form.Label>
            {settings.default_country_code ? (
              <PhoneInput
                defaultCountry={settings.default_country_code}
                required={optionFields.phoneIsRequired}
                value={watchPhone}
                onChange={(value) => setValue("phone", value)}
              />
            ) : (
              <Form.Control
                {...register(
                  "phone",
                  validate.phoneOptions(optionFields.phoneIsRequired),
                )}
              />
            )}
            {errors.phone && (
              <FieldErrorMessage message={errors.phone.message} />
            )}
          </Form.Group>
        )}

        {!optionFields.zipcodeIsHidden && (
          <Row className="form-group mb-4">
            <Col sm={6}>
              <Form.Label>
                {__("Zipcode", "yoyaku-manager")}
                {optionFields.zipcodeIsRequired && <RequiredLabel />}
              </Form.Label>
              <Form.Control
                {...register(
                  "zipcode",
                  validate.zipcodeOptions(optionFields.zipcodeIsRequired),
                )}
              />
              {errors.zipcode && (
                <FieldErrorMessage message={errors.zipcode.message} />
              )}
            </Col>
          </Row>
        )}

        {!optionFields.addressIsHidden && (
          <Form.Group className="form-group mb-4">
            <Form.Label>
              {__("Address", "yoyaku-manager")}
              {optionFields.address.required && <RequiredLabel />}
            </Form.Label>
            <Form.Control
              {...register(
                "address",
                validate.addressOptions(optionFields.addressIsRequired),
              )}
            />
            {errors.address && (
              <FieldErrorMessage message={errors.address.message} />
            )}
          </Form.Group>
        )}

        {!optionFields.birthdayIsHidden && (
          <Row className="form-group mb-4">
            <Col sm={6}>
              <Form.Label>
                {__("Birthday", "yoyaku-manager")}
                {optionFields.birthday.required && <RequiredLabel />}
              </Form.Label>
              <Form.Control
                type="date"
                {...register(
                  "birthday",
                  validate.birthdayOptions(optionFields.birthdayIsRequired),
                )}
              />
              {errors.birthday && (
                <FieldErrorMessage message={errors.birthday.message} />
              )}
            </Col>
          </Row>
        )}

        {!optionFields.genderIsHidden && (
          <Row className="form-group mb-4">
            <Col sm={6}>
              <Form.Label>
                {__("Gender", "yoyaku-manager")}
                {optionFields.gender.required && <RequiredLabel />}
              </Form.Label>
              <Form.Select
                {...register(
                  "gender",
                  validate.genderOptions(optionFields.genderIsRequired),
                )}
              >
                {!optionFields.genderIsRequired && (
                  <option value={gender.unknown}></option>
                )}
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
      </div>
    </>
  );
};
