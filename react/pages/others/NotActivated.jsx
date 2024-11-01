import { __ } from "@wordpress/i18n";
import { Button } from "react-bootstrap";
import { Header } from "@/components/layout";

/**
 *
 * @returns {JSX.Element}
 * @constructor
 */
export const NotActivated = () => {
  return (
    <div>
      <Header title={__("This plugin is not activated.", "yoyaku-manager")} />
      <p>{__("Please activate this plugin.", "yoyaku-manager")}</p>
      <Button
        variant="primary"
        href={`${window.location.pathname}?page=yoyaku-settings`}
      >
        {__("Go To Settings", "yoyaku-manager")}
      </Button>
    </div>
  );
};
