import {__} from "@wordpress/i18n";

/**
 * キャンセル完了ページ
 * @returns {JSX.Element}
 * @constructor
 */
export const Completion = () => {
  return (
    <>
      <strong>{__("Your booking has been canceled.", "yoyaku-manager")}</strong>
    </>
  );
};
