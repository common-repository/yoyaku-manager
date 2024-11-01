import { showInfoToast } from "@/components/common";
import { __ } from "@wordpress/i18n";
import { settings } from "./settings";

const defaultPlaceholders = {
  booking: [
    {
      value: "%customer_email%",
      label: __("Customer email", "yoyaku-manager"),
    },
    {
      value: "%customer_first_name%",
      label: __("Customer first name", "yoyaku-manager"),
    },
    {
      value: "%customer_last_name%",
      label: __("Customer last name", "yoyaku-manager"),
    },
    {
      value: "%customer_full_name%",
      label: __("Customer full name", "yoyaku-manager"),
    },
    {
      value: "%booking_price%",
      label: __("Payment amount", "yoyaku-manager"),
    },
    {
      value: "%booking_cancel_url%",
      label: __("Booking cancel URL", "yoyaku-manager"),
    },
  ],

  event: [
    {
      value: "%event_name%",
      label: __("Event name", "yoyaku-manager"),
    },
    {
      value: "%event_description%",
      label: __("Event description", "yoyaku-manager"),
    },
    {
      value: "%event_location%",
      label: __("Location name", "yoyaku-manager"),
    },
    {
      value: "%event_start_datetime%",
      label: __("Start date and time of the event", "yoyaku-manager"),
    },
    {
      value: "%event_end_datetime%",
      label: __("End date and time of the event", "yoyaku-manager"),
    },
    {
      value: "%time_zone%",
      label: __("Timezone", "yoyaku-manager"),
    },
    {
      value: "%event_tickets%",
      label: __("Event tickets", "yoyaku-manager"),
    },
    {
      value: "%online_meeting_url%",
      label: __("Online meeting URL", "yoyaku-manager"),
    },
    {
      value: "%google_meet_url%",
      label: __("Google meet URL", "yoyaku-manager"),
    },
    {
      value: "%zoom_join_url%",
      label: __("Zoom join URL", "yoyaku-manager"),
    },
  ],
};

/**
 * 選択したプレースホルダーをクリップボードにコピーする
 * @param code
 */
export const copyPlaceholder = (code) => {
  let textArea = document.createElement("textarea");
  textArea.value = code;
  document.body.appendChild(textArea);
  textArea.select();
  document.execCommand("Copy");
  document.body.removeChild(textArea);

  showInfoToast(__("Copied", "yoyaku-manager"), "no-title");
};

/**
 * 顧客系のプレースホルダーを取得
 * @returns object
 */
export const getCustomerPlaceholders = () => {
  let result = defaultPlaceholders.booking.concat();
  const optionFieldSettings = settings.getOptionFieldSettings();

  if (!optionFieldSettings.phoneIsHidden) {
    result.push({
      value: "%customer_phone%",
      label: __("Customer phone", "yoyaku-manager"),
    });
  }

  return result;
};

/**
 * イベント系のプレースホルダーを取得
 * @returns object
 */
export const getEventPlaceholders = () => {
  return defaultPlaceholders.event.concat();
};
