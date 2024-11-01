import { __ } from "@wordpress/i18n";

export default {
  getMinutes(amountTime, interval) {
    switch (interval) {
      case "hours":
        return amountTime * 60;
      case "days":
        return amountTime * 3600;
    }
  },

  fromMinutes(minutes, interval) {
    switch (interval) {
      case "hours":
        return Math.round(minutes / 60);
      case "days":
        return Math.round(minutes / 3600);
    }
  },

  minutesToNiceDuration(minutes) {
    const hours = Math.floor(minutes / 60);
    const minutePart = minutes % 60;
    const hours_str = hours ? hours + __("h", "yoyaku-manager") : "";
    const minutes_str = minutePart
      ? minutePart + __("min", "yoyaku-manager")
      : "";
    return hours_str + " " + minutes_str;
  },
};
