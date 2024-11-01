import { __ } from "@wordpress/i18n";
import toast from "react-hot-toast";

const showInfoToast = (message = "", customClass = "") => {
  toast(message, {
    position: "top-right",
    className: customClass,
  });
};

const showSuccessToast = (message = "", customClass = "") => {
  const defaultMessage = __("Saved!", "yoyaku-manager");
  toast.success(message || defaultMessage, {
    position: "top-right",
    className: customClass,
  });
};

const showErrorToast = (message, customClass = "") => {
  toast.error(message, {
    position: "top-right",
    className: customClass,
  });
};

export { showErrorToast, showSuccessToast, showInfoToast };
