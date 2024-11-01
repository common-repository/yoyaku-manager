import {useState} from "@wordpress/element";
import {__} from "@wordpress/i18n";
import {Button, Col, Form, Row} from "react-bootstrap";

/**
 *
 * @param searchText
 * @param setSearchText
 * @returns {JSX.Element}
 * @constructor
 */
export const SearchForm = ({ searchText, setSearchText }) => {
  const [text, setText] = useState(searchText);

  return (
    <Row xs="auto" className="justify-content-end mb-2">
      <Col className="pe-0">
        <Form.Control
          type="search"
          className="y-sort-wrap"
          value={text}
          onChange={(e) => setText(e.target.value)}
        />
      </Col>
      <Col>
        <Button variant="outline-primary" onClick={() => setSearchText(text)}>
          {__("Search", "yoyaku-manager")}
        </Button>
      </Col>
    </Row>
  );
};
