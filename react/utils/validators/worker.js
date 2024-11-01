import { getMaxLengthMessage, NAME_MAX_LENGTH } from "@/utils/validators";

export const googleCalendarIdOptions = {
  maxLength: {
    value: NAME_MAX_LENGTH,
    message: getMaxLengthMessage(NAME_MAX_LENGTH),
  },
};
