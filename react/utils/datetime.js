/**
 * https://www.npmjs.com/package/@wordpress/date
 */
import { dateI18n, getSettings } from "@wordpress/date";

export default {
  getLocale() {
    const settings = getSettings();
    return settings.l10n.locale;
  },

  /**
   * 日時ををRFC3339形式に変換する
   * 例) イベント期間の開始日時の yyyy-mm-ddTHH:mm 形式をAPIパラメーター用に変換する
   * @param datetime Date object or string, parsable by moment.js
   * @returns {String} RFC3339形式
   */
  getRFC3339String(datetime) {
    return dateI18n("Y-m-d\\TH:i:sP", datetime);
  },

  /**
   * 一般設定の日付形式を使った日付を取得する
   * @param datetime Date object or string, parsable by moment.js
   * @returns {string}
   */
  getWpFormattedDateString(datetime) {
    const settings = getSettings();
    return dateI18n(settings.formats.date, datetime);
  },

  /**
   * 一般設定の時刻形式を使った時間を取得する
   * @param datetime Date object or string, parsable by moment.js
   * @returns {string}
   */
  getWpFormattedTimeString(datetime) {
    const settings = getSettings();
    return dateI18n(settings.formats.time, datetime, settings.timezone.offset);
  },

  /**
   * 一般設定の日付形式と時刻形式を使った日時を取得する
   * @param datetime
   * @returns {string}
   */
  getWpFormattedDateTimeString(datetime) {
    if (!datetime) {
      return "";
    }

    const settings = getSettings();
    const dateFormat = `${settings.formats.date} ${settings.formats.time}`;
    return dateI18n(dateFormat, datetime, settings.timezone.offset);
  },

  /**
   * hh:mm:ss 形式を hh:mm形式にする
   * @param time {string}
   */
  formatTimeRemoveSecond(time) {
    let time_parts = time.split(":");
    if (2 <= time_parts.length) {
      return `${time_parts[0]}:${time_parts[1]}`;
    } else {
      return time;
    }
  },
};
