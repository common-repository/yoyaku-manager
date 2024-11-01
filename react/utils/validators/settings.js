import { __ } from "@wordpress/i18n";
import {
  EMAIL_MAX_LENGTH,
  getMaxLengthMessage,
  getMaxMessage,
  getMinMessage,
  getRequiredMessage,
} from "./index";

const requiredOnlyOptions = {
  required: getRequiredMessage(),
};

//-------------------------------
// general
//-------------------------------

/**
 * 大文字英語2文字の国名コード
 * https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes
 */
export const defaultCountryCodeOptions = {
  pattern: {
    value: /[A-Z]{2}/,
    message: __("Enter two-letter ISO country code.", "yoyaku-manager"),
  },
};

//-------------------------------
// notifications
//-------------------------------

// 形式チェックは <input type="email"> を使う
export const senderEmailOptions = {
  required: getRequiredMessage(),
  maxLength: {
    value: EMAIL_MAX_LENGTH,
    message: getMaxLengthMessage(EMAIL_MAX_LENGTH),
  },
};

export const smtpPortOptions = {
  required: getRequiredMessage(),
  valueAsNumber: true,
  min: { value: 0, message: getMinMessage(0) },
  max: {
    value: 65535,
    message: getMaxMessage(65535),
  },
};

export const smtpHostOptions = requiredOnlyOptions;
export const smtpUsernameOptions = requiredOnlyOptions;
export const smtpPasswordOptions = requiredOnlyOptions;

//-------------------------------
// payments
//-------------------------------

export const symbolOptions = requiredOnlyOptions;
export const priceDecimalsOptions = {
  valueAsNumber: true,
  min: { value: 0, message: getMinMessage(0) },
};
