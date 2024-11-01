import { BasePage } from "@/gutenberg-blocks/components/layout";
import { __ } from "@wordpress/i18n";

/**
 * 予約完了ページ
 * @returns {JSX.Element}
 * @constructor
 */
export const Completion = () => {
  return (
    <BasePage>
      <p>
        <strong>{__("Thank you!", "yoyaku-manager")}</strong>
      </p>
      <p>
        <strong>{__("Reservation completed.", "yoyaku-manager")}</strong>
      </p>
    </BasePage>
  );
};
