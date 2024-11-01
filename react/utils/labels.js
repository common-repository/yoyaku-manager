import { __, _x } from "@wordpress/i18n";
import {
  bookingStatus,
  gatewayType,
  gender,
  notificationTiming,
  optionFieldStatus,
  paymentStatus,
} from "./consts";

export default {
  getBookingStatusLabel(status) {
    switch (status) {
      case bookingStatus.approved:
        return __("Approved", "yoyaku-manager");

      case bookingStatus.pending:
        return __("Pending", "yoyaku-manager");

      case bookingStatus.canceled:
        return __("Canceled", "yoyaku-manager");

      case bookingStatus.disapproved:
        return __("Disapproved", "yoyaku-manager");
    }
  },

  getNotificationTimingLabel(status) {
    switch (status) {
      case notificationTiming.approved:
        return _x("Approved", "notificationTiming", "yoyaku-manager");

      case notificationTiming.pending:
        return _x("Pending", "notificationTiming", "yoyaku-manager");

      case notificationTiming.canceled:
        return _x("Canceled", "notificationTiming", "yoyaku-manager");

      case notificationTiming.disapproved:
        return _x("Disapproved", "notificationTiming", "yoyaku-manager");

      case notificationTiming.scheduled:
        return _x("Scheduled", "notificationTiming", "yoyaku-manager");
    }
  },

  getPaymentStatusLabel(status) {
    switch (status) {
      case paymentStatus.paid:
        return __("Paid", "yoyaku-manager");

      case paymentStatus.pending:
        return __("Pending Payment", "yoyaku-manager");

      case paymentStatus.refunded:
        return __("Refunded", "yoyaku-manager");
    }
  },

  /**
   * gatewayのラベルを取得
   * on_siteの場合は翻訳した文字列、stripeの場合はStripeにする
   * @param gateway
   * @returns {string}
   */
  getGatewayLabel(gateway) {
    if (gateway === gatewayType.on_site) {
      return __("On Site", "yoyaku-manager");
    } else {
      return gateway.charAt(0).toUpperCase() + gateway.slice(1).toLowerCase();
    }
  },

  getGenderLabel(status) {
    switch (status) {
      case gender.unknown:
        return "";

      case gender.male:
        return __("Male", "yoyaku-manager");

      case gender.female:
        return __("Female", "yoyaku-manager");
    }
  },

  getOptionFieldStatusLabel(status) {
    switch (status) {
      case optionFieldStatus.hidden:
        return __("Hidden", "yoyaku-manager");

      case optionFieldStatus.optional:
        return __("Optional", "yoyaku-manager");

      case optionFieldStatus.required:
        return __("Required", "yoyaku-manager");
    }
  },
};
