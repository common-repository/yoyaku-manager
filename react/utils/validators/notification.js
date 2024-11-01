import {
  getMaxLengthMessage,
  getMinMessage,
  getRequiredMessage,
  NAME_MAX_LENGTH,
} from "./index";

export const nameOptions = {
  required: getRequiredMessage(),
  maxLength: {
    value: NAME_MAX_LENGTH,
    message: getMaxLengthMessage(NAME_MAX_LENGTH),
  },
};

export const timingOptions = {
  required: getRequiredMessage(),
};

export const isBeforeOptions = {
  // required: getRequiredMessage(),
  // 初期値の型はbool, フォーム上で変更した時の型はstringになる
  setValueAs: (v) => [true, "true"].includes(v),
};

export const daysOptions = {
  valueAsNumber: true,
  required: getRequiredMessage(),
  min: { value: 0, message: getMinMessage(0) },
};

export const timeOptions = {
  required: getRequiredMessage(),
  setValueAs: (v) => {
    if (typeof v !== "string") {
      return "00:00:00";
    } else if (v.match("^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$")) {
      // apiで取得した時の形式
      return v;
    } else if (v.match("^([01][0-9]|2[0-3]):[0-5][0-9]$")) {
      // 画面で時間設定した時の形式
      return v + ":00";
    } else {
      // 通常発生しない操作
      return "00:00:00";
    }
  },
};

export const subjectOptions = {
  required: getRequiredMessage(),
  maxLength: {
    value: NAME_MAX_LENGTH,
    message: getMaxLengthMessage(NAME_MAX_LENGTH),
  },
};
