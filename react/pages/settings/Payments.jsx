import { __ } from "@wordpress/i18n";
import { Col, Form, Row } from "react-bootstrap";
import { useForm } from "react-hook-form";
import { FieldErrorMessage, SaveBtn } from "@/components/common";
import { BasePage } from "@/components/layout";
import { currencyISOList } from "@/utils/consts";
import {
  priceDecimalsOptions,
  symbolOptions,
} from "@/utils/validators/settings";

export const Payments = ({ values, onSubmit }) => {
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
            <Form.Label column sm={4}>
              {__("Symbol", "yoyaku-manager")}
            </Form.Label>
            <Col sm={4}>
              <Form.Control {...register("symbol", symbolOptions)} />
              {errors.symbol && (
                <FieldErrorMessage message={errors.symbol.message} />
              )}
            </Col>
          </Form.Group>

          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={4}>
              {__("Price Symbol Position", "yoyaku-manager")}
            </Form.Label>
            <Col sm={4}>
              <Form.Select {...register("price_symbol_position")}>
                <option value="before">{__("Before", "yoyaku-manager")}</option>
                <option value="after">{__("After", "yoyaku-manager")}</option>
              </Form.Select>
            </Col>
          </Form.Group>

          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={4}>
              {__("Price Thousand Separator", "yoyaku-manager")}
            </Form.Label>
            <Col sm={4}>
              <Form.Control {...register("price_thousand_separator")} />
            </Col>
          </Form.Group>

          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={4}>
              {__("Price Decimals", "yoyaku-manager")}
            </Form.Label>
            <Col sm={4}>
              <Form.Control
                type="number"
                min={0}
                max={3}
                {...register("price_decimals", priceDecimalsOptions)}
              />
              {errors.price_decimals && (
                <FieldErrorMessage message={errors.price_decimals.message} />
              )}
            </Col>
          </Form.Group>

          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={4}>
              {__("Price Decimal Separator", "yoyaku-manager")}
            </Form.Label>
            <Col sm={4}>
              <Form.Control {...register("price_decimal_separator")} />
            </Col>
          </Form.Group>

          <hr />
          <h2 className="mb-3">Stripe</h2>

          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={4}>
              {__("Currency", "yoyaku-manager")}
            </Form.Label>
            <Col sm={4}>
              <Form.Select {...register("currency")}>
                <option value=""></option>
                {currencyISOList.map((currency) => (
                  <option key={currency} value={currency}>
                    {currency}
                  </option>
                ))}
              </Form.Select>
              {errors.currency && (
                <FieldErrorMessage message={errors.currency.message} />
              )}
            </Col>
          </Form.Group>

          <Form.Check
            className="form-group"
            label={__("Stripe Test Mode", "yoyaku-manager")}
            {...register("stripe_test_mode")}
          />

          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={4}>
              {__("Live Publishable Key", "yoyaku-manager")}
            </Form.Label>
            <Col sm={6}>
              <Form.Control {...register("stripe_live_publishable_key")} />
            </Col>
          </Form.Group>

          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={4}>
              {__("Live Secret Key", "yoyaku-manager")}
            </Form.Label>
            <Col sm={6}>
              <Form.Control {...register("stripe_live_secret_key")} />
            </Col>
          </Form.Group>

          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={4}>
              {__("Test Publishable Key", "yoyaku-manager")}
            </Form.Label>
            <Col sm={6}>
              <Form.Control {...register("stripe_test_publishable_key")} />
            </Col>
          </Form.Group>

          <Form.Group key={name} as={Row} className="form-group">
            <Form.Label column sm={4}>
              {__("Test Secret Key", "yoyaku-manager")}
            </Form.Label>
            <Col sm={6}>
              <Form.Control {...register("stripe_test_secret_key")} />
            </Col>
          </Form.Group>

          <SaveBtn />
        </Form>
      </div>
    </BasePage>
  );
};

export const getPaymentsSetting = (settings) => {
  return {
    currency: settings.currency,
    symbol: settings.symbol,
    price_symbol_position: settings.price_symbol_position,
    price_decimals: settings.price_decimals,
    price_thousand_separator: settings.price_thousand_separator,
    price_decimal_separator: settings.price_decimal_separator,
    stripe_test_mode: settings.stripe_test_mode,
    stripe_live_secret_key: settings.stripe_live_secret_key,
    stripe_live_publishable_key: settings.stripe_live_publishable_key,
    stripe_test_secret_key: settings.stripe_test_secret_key,
    stripe_test_publishable_key: settings.stripe_test_publishable_key,
  };
};
