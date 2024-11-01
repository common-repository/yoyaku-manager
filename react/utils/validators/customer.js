import {
  ADDRESS_MAX_LENGTH,
  EMAIL_MAX_LENGTH,
  getBirthDayFormatMessage,
  getInvalidPatternMessage,
  getMaxLengthMessage,
  getRequiredMessage,
  MEMO_MAX_LENGTH,
  NAME_MAX_LENGTH,
  PHONE_MAX_LENGTH,
  ZIPCODE_MAX_LENGTH,
} from "./index";

export const nameOptions = {
  required: getRequiredMessage(),
  maxLength: {
    value: NAME_MAX_LENGTH,
    message: getMaxLengthMessage(NAME_MAX_LENGTH),
  },
};

// 形式チェックは <input type="email"> を使う
export const emailOptions = {
  required: getRequiredMessage(),
  maxLength: {
    value: EMAIL_MAX_LENGTH,
    message: getMaxLengthMessage(EMAIL_MAX_LENGTH),
  },
};

export const rubyOptions = (required) => {
  let result = {
    maxLength: {
      value: NAME_MAX_LENGTH,
      message: getMaxLengthMessage(NAME_MAX_LENGTH),
    },
  };
  if (required) {
    result.required = getRequiredMessage();
  }
  return result;
};

export const phoneOptions = (required) => {
  let result = {
    maxLength: {
      value: PHONE_MAX_LENGTH,
      message: getMaxLengthMessage(PHONE_MAX_LENGTH),
    },
    pattern: {
      value: /(|\+?[0-9-]+)/,
      message: getInvalidPatternMessage(),
    },
  };
  if (required) {
    result.required = getRequiredMessage();
  }
  return result;
};

export const birthdayOptions = (required) => {
  let result = {
    pattern: {
      value: /^\d{4}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/,
      message: getBirthDayFormatMessage(),
    },
  };
  if (required) {
    result.required = getRequiredMessage();
  }
  return result;
};

export const genderOptions = (required) => {
  let result = {};
  if (required) {
    result.required = getRequiredMessage();
  }
  return result;
};

export const zipcodeOptions = (required) => {
  let result = {
    maxLength: {
      value: ZIPCODE_MAX_LENGTH,
      message: getMaxLengthMessage(ZIPCODE_MAX_LENGTH),
    },
  };
  if (required) {
    result.required = getRequiredMessage();
  }
  return result;
};

export const addressOptions = (required) => {
  let result = {
    maxLength: {
      value: ADDRESS_MAX_LENGTH,
      message: getMaxLengthMessage(ADDRESS_MAX_LENGTH),
    },
  };
  if (required) {
    result.required = getRequiredMessage();
  }
  return result;
};

export const memoOptions = {
  maxLength: {
    value: MEMO_MAX_LENGTH,
    message: getMaxLengthMessage(MEMO_MAX_LENGTH),
  },
};
