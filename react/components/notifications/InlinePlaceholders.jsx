import {
  copyPlaceholder,
  getCustomerPlaceholders,
  getEventPlaceholders,
} from "@/utils/placeholders";
import { __ } from "@wordpress/i18n";
import { Col, ListGroup, Row } from "react-bootstrap";

const InlinePlaceholders = ({ notification }) => {
  if (!notification) return null;

  const customerPlaceholders = getCustomerPlaceholders();
  const eventPlaceholders = getEventPlaceholders();

  return (
    <div>
      <hr />
      <h2 className="mt-3 mb-1">{__("Placeholders", "yoyaku-manager")}</h2>
      <Row xs="auto">
        <Col>
          <p className="mb-1">{__("Customer", "yoyaku-manager")}</p>
          <ListGroup>
            {customerPlaceholders.map((code, placeholderKey) => (
              <ListGroup.Item
                key={placeholderKey}
                action
                onClick={() => copyPlaceholder(code.value)}
              >
                {code.value}
              </ListGroup.Item>
            ))}
          </ListGroup>
        </Col>
        <Col>
          <p className="mb-1">{__("Event", "yoyaku-manager")}</p>
          <ListGroup>
            {eventPlaceholders.map((code, placeholderKey) => (
              <ListGroup.Item
                key={placeholderKey}
                action
                onClick={() => copyPlaceholder(code.value)}
              >
                {code.value}
              </ListGroup.Item>
            ))}
          </ListGroup>
        </Col>
      </Row>
    </div>
  );
};

export default InlinePlaceholders;
