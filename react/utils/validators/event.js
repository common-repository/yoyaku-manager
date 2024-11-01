import dt from "@/utils/datetime";
import { __, sprintf } from "@wordpress/i18n";
import {
  ADDRESS_MAX_LENGTH,
  DESCRIPTION_MAX_LENGTH,
  getMaxLengthMessage,
  getMinMessage,
  getRequiredMessage,
  NAME_MAX_LENGTH,
  URL_MAX_LENGTH,
} from "./index";

//------------------------------------
// event
//------------------------------------

/**
 * イベント名、チケット名共通
 * @type {{name: string, options: {required: string, maxLength: {message, value: number}}}}
 */
export const nameOptions = {
  required: getRequiredMessage(),
  maxLength: {
    value: NAME_MAX_LENGTH,
    message: getMaxLengthMessage(NAME_MAX_LENGTH),
  },
};

export const minTimeToCloseBookingOptions = {
  valueAsNumber: true,
  required: getRequiredMessage(),
  min: { value: 0, message: getMinMessage(0) },
};

export const minTimeToCancelBookingOptions = {
  valueAsNumber: true,
  required: getRequiredMessage(),
  min: { value: 0, message: getMinMessage(0) },
};

export const maxTicketsPerBookingOption = {
  valueAsNumber: true,
  required: getRequiredMessage(),
  min: { value: 1, message: getMinMessage(1) },
};

export const redirectUrlOptions = {
  maxLength: {
    value: URL_MAX_LENGTH,
    message: getMaxLengthMessage(URL_MAX_LENGTH),
  },
};

export const zoomUserIdOptions = {
  maxLength: {
    value: NAME_MAX_LENGTH,
    message: getMaxLengthMessage(NAME_MAX_LENGTH),
  },
};

export const descriptionOptions = {
  maxLength: {
    value: DESCRIPTION_MAX_LENGTH,
    message: getMaxLengthMessage(DESCRIPTION_MAX_LENGTH),
  },
};

export const notificationIdsOptions = {
  setValueAs: (v) => parseInt(v),
};

//------------------------------------
// period
//------------------------------------

export const startDatetimeOptions = {
  required: getRequiredMessage(),
  setValueAs: dt.getRFC3339String,
  validate: {
    validateBeforeEndDt: (_, values) => {
      const startDt = new Date(values.start_datetime);
      const endDt = new Date(values.end_datetime);
      return (
        startDt.getTime() < endDt.getTime() ||
        sprintf(
          __("Start DateTime is later than End DateTime.", "yoyaku-manager"),
        )
      );
    },
  },
};

export const datetimeOptions = {
  required: getRequiredMessage(),
  setValueAs: dt.getRFC3339String,
};

export const maxTicketCountOptions = {
  required: getRequiredMessage(),
  valueAsNumber: true,
  min: { value: 1, message: getMinMessage(1) },
};

export const locationOptions = {
  maxLength: {
    value: ADDRESS_MAX_LENGTH,
    message: getMaxLengthMessage(ADDRESS_MAX_LENGTH),
  },
};

export const zoomMeetingOptions = {
  maxLength: {
    value: DESCRIPTION_MAX_LENGTH,
    message: getMaxLengthMessage(DESCRIPTION_MAX_LENGTH),
  },
};

export const googleCalendarEventIdOptions = {
  maxLength: {
    value: 255,
    message: getMaxLengthMessage(255),
  },
};

export const googleMeetUrlOptions = {
  maxLength: {
    value: 255,
    message: getMaxLengthMessage(255),
  },
};

//------------------------------------
// ticket
//------------------------------------

export const priceOptions = {
  valueAsNumber: true,
  required: getRequiredMessage(),
  min: { value: 0, message: getMinMessage(0) },
};

export const ticketCountOptions = {
  valueAsNumber: true,
  required: getRequiredMessage(),
  min: { value: 0, message: getMinMessage(0) },
};

export const generateTicketsOptions = (maxTotalTickets) => {
  return {
    valueAsNumber: true,
    validate: {
      validateLimit: (_, values) => {
        const total = values.tickets.reduce(
          (sum, item) => sum + item.buy_count,
          0,
        );

        return (
          total <= maxTotalTickets ||
          sprintf(
            /* translators: %d is maxTotalTickets */
            __("Please limit the total to %d or less.", "yoyaku-manager"),
            maxTotalTickets,
          )
        );
      },
      validateMin: (_, values) => {
        const total = values.tickets.reduce(
          (sum, item) => sum + item.buy_count,
          0,
        );

        return (
          0 < total ||
          sprintf(
            __("Please select a ticket.", "yoyaku-manager"),
            maxTotalTickets,
          )
        );
      },
    },
  };
};

//------------------------------------
// booking
//------------------------------------