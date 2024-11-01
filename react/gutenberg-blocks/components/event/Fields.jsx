import { settings } from "@/utils/settings";
import { __ } from "@wordpress/i18n";
import { Form } from "react-bootstrap";

export const AcceptTermsOfServiceField = ({
  acceptTermsOfService,
  changeAcceptTermsOfService,
}) => {
  if (settings.terms_of_service_url) {
    return (
      <>
        <Form.Group className="form-group">
          <Form.Check
            checked={acceptTermsOfService}
            onChange={changeAcceptTermsOfService}
            className="yoyaku-accept-terms-of-service"
            label={
              <p>
                <a href={settings.terms_of_service_url}>
                  {__("Accept terms of service.", "yoyaku-manager")}
                </a>
              </p>
            }
          />
        </Form.Group>
      </>
    );
  } else {
    return null;
  }
};
