import { __, _n, sprintf } from "@wordpress/i18n";

//
// フィールドの定数
//
export const NAME_MAX_LENGTH = 255;
export const EMAIL_MAX_LENGTH = 255;
export const ADDRESS_MAX_LENGTH = 255;
export const PHONE_MAX_LENGTH = 30;
export const MEMO_MAX_LENGTH = 5000;
export const DESCRIPTION_MAX_LENGTH = 4000;
export const URL_MAX_LENGTH = 4096;
export const COLOR_MAX_LENGTH = 255;
export const ZIPCODE_MAX_LENGTH = 20;

export const getRequiredMessage = () => {
  return __("This field is required.", "yoyaku-manager");
};

/**
 * 注) translators: のコメントは、pot生成時にWarningが発生しないようにするために必要
 * @param minLength
 * @returns {string}
 */
export const getMinLengthMessage = (minLength) => {
  return sprintf(
    /* translators: %d is replaced with "number" */
    _n(
      "The minimum length for this field is %d character.",
      "The minimum length for this field is %d characters.",
      minLength,
      "yoyaku-manager",
    ),
    minLength,
  );
};

export const getMaxLengthMessage = (maxLength) => {
  return sprintf(
    /* translators: %d is replaced with "number" */
    _n(
      "The maximum length for this field is %d character.",
      "The maximum length for this field is %d characters.",
      maxLength,
      "yoyaku-manager",
    ),
    maxLength,
  );
};

export const getMaxMessage = (max) => {
  return sprintf(
    /* translators: %d is replaced with "number" */
    __("This field is less than or equal to %d.", "yoyaku-manager"),
    max,
  );
};

export const getMinMessage = (min) => {
  return sprintf(
    /* translators: %d is replaced with "number" */
    __("This field is greater than or equal to %d.", "yoyaku-manager"),
    min,
  );
};

export const getInvalidPatternMessage = () => {
  return __("Invalid pattern.", "yoyaku-manager");
};

export const getBirthDayFormatMessage = () => {
  return __("Please enter in the format: YYYY-MM-DD", "yoyaku-manager");
};
