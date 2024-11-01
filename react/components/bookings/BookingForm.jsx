import { updateEventBookingAPI } from "@/api";
import {
  APIErrorMessage,
  FieldErrorMessage,
  RequiredLabel,
  SaveBtn,
  showSuccessToast,
} from "@/components/common";
import { gatewayType, gender, paymentStatus } from "@/utils/consts";
import label from "@/utils/labels";
import { getFormattedPrice } from "@/utils/price";
import { settings } from "@/utils/settings";
import * as validateCustomer from "@/utils/validators/customer";
import * as validateEvent from "@/utils/validators/event";
import { ticketCountOptions } from "@/utils/validators/event";
import { useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { Col, Form, Row } from "react-bootstrap";
import { useFieldArray, useForm } from "react-hook-form";
import PhoneInput from "react-phone-number-input";
import { useNavigate } from "react-router-dom";

/**
 *
 * @param defaultValues
 * @param tickets
 * @param dataHandler
 * @param navigateTo
 * @returns {JSX.Element}
 * @constructor
 */
export const BookingForm = ({
  defaultValues,
  tickets,
  dataHandler,
  navigateTo,
}) => {
  const isUpdate = dataHandler === updateEventBookingAPI;
  const optionFields = settings.getOptionFieldSettings();
  const navigate = useNavigate();
  const [errorResponse, setErrorResponse] = useState();
  const {
    control,
    register,
    watch,
    setValue,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm({ values: defaultValues });

  const { fields: ticketFields } = useFieldArray({
    control,
    name: "tickets",
  });
  const watchPhone = watch("phone", "");

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
            <Form.Control
              {...register("first_name", validateCustomer.nameOptions)}
            />
            {errors.first_name && (
              <FieldErrorMessage message={errors.first_name.message} />
            )}
          </Form.Group>
          <Form.Group as={Col}>
            <Form.Label>
              {__("Last Name", "yoyaku-manager")}
              <RequiredLabel />
            </Form.Label>
            <Form.Control
              {...register("last_name", validateCustomer.nameOptions)}
            />
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
                {...register(
                  "first_name_ruby",
                  validateCustomer.rubyOptions(false),
                )}
              />
              {errors.first_name_ruby && (
                <FieldErrorMessage message={errors.first_name_ruby.message} />
              )}
            </Form.Group>
            <Form.Group as={Col}>
              <Form.Label>{__("Last Name Ruby", "yoyaku-manager")}</Form.Label>
              <Form.Control
                {...register(
                  "last_name_ruby",
                  validateCustomer.rubyOptions(false),
                )}
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
            {...register("email", validateCustomer.emailOptions)}
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
                  value={watchPhone}
                  onChange={(value) => setValue("phone", value)}
                />
              ) : (
                <Form.Control
                  {...register("phone", validateCustomer.phoneOptions(false))}
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
                {...register("zipcode", validateCustomer.zipcodeOptions(false))}
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
              {...register("address", validateCustomer.addressOptions(false))}
            />
            {errors.address && (
              <FieldErrorMessage message={errors.address.message} />
            )}
          </Form.Group>
        )}

        {/* 性別と誕生日のoptionFields設定に応じて最大２つのフォームを並べる */}
        {(!optionFields.birthdayIsHidden || !optionFields.genderIsHidden) && (
          <Row>
            {!optionFields.birthdayIsHidden && (
              <Col sm={6} className="form-group">
                <Form.Label>{__("Birthday", "yoyaku-manager")}</Form.Label>
                <Form.Control
                  {...register(
                    "birthday",
                    validateCustomer.birthdayOptions(false),
                  )}
                />
                {errors.birthday && (
                  <FieldErrorMessage message={errors.birthday.message} />
                )}
              </Col>
            )}
            {!optionFields.genderIsHidden && (
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
                  {errors.gender && (
                    <FieldErrorMessage message={errors.gender.message} />
                  )}
                </Form.Select>
              </Col>
            )}
          </Row>
        )}

        {isUpdate && (
          <Form.Check
            className="form-group"
            label={__("Also Update Customer Data", "yoyaku-manager")}
            {...register("update_customer")}
          />
        )}

        <Form.Group className="form-group">
          <Form.Label>{__("Memo", "yoyaku-manager")}</Form.Label>
          <Form.Control
            as="textarea"
            rows={3}
            {...register("memo", validateCustomer.memoOptions)}
          />
          {errors.memo && <FieldErrorMessage message={errors.memo.message} />}
        </Form.Group>

        <Form.Group className="form-group">
          <Form.Label>{__("Payment Status", "yoyaku-manager")}</Form.Label>
          <Form.Select {...register("payment_status")}>
            <option value={paymentStatus.paid}>
              {label.getPaymentStatusLabel(paymentStatus.paid)}
            </option>
            <option value={paymentStatus.pending}>
              {label.getPaymentStatusLabel(paymentStatus.pending)}
            </option>
            <option value={paymentStatus.refunded}>
              {label.getPaymentStatusLabel(paymentStatus.refunded)}
            </option>
            {errors.payment_status && (
              <FieldErrorMessage message={errors.payment_status.message} />
            )}
          </Form.Select>
        </Form.Group>

        <Form.Group className="form-group">
          <Form.Label>{__("Gateway", "yoyaku-manager")}</Form.Label>
          <Form.Select {...register("gateway")}>
            <option value={gatewayType.on_site}>
              {label.getGatewayLabel(gatewayType.on_site)}
            </option>
            <option value={gatewayType.stripe}>
              {label.getGatewayLabel(gatewayType.stripe)}
            </option>
            {errors.gateway && (
              <FieldErrorMessage message={errors.gateway.message} />
            )}
          </Form.Select>
        </Form.Group>

        <h2 className="mb-3">
          {__("Tickets Count & Payment Amount", "yoyaku-manager")}
        </h2>

        {ticketFields.map((ticketField, index) => (
          <Row key={ticketField.id}>
            <Col sm={6} className="form-group">
              <Form.Label>
                {tickets[index].name} {getFormattedPrice(tickets[index].price)}{" "}
              </Form.Label>
              <Form.Control
                type="number"
                min={0}
                {...register(`tickets.${index}.buy_count`, ticketCountOptions)}
              />
              {errors.tickets?.[index].buy_count && (
                <FieldErrorMessage
                  message={errors.tickets[index].buy_count.message}
                />
              )}
            </Col>
          </Row>
        ))}

        <Row>
          <Col sm={6} className="form-group">
            <Form.Label>
              {sprintf(
                /* translators: %s is symbol */
                __("Payment Amount (%s)", "yoyaku-manager"),
                settings.symbol,
              )}
            </Form.Label>
            <Form.Control {...register("amount", validateEvent.priceOptions)} />
            {errors.amount && (
              <FieldErrorMessage message={errors.amount.message} />
            )}
          </Col>
        </Row>

        <SaveBtn isSubmitting={isSubmitting} />
      </Form>
    </div>
  );
};
