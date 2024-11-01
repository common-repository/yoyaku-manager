import { Col, Row } from "react-bootstrap";

export const CardRow = (props) => {
  const { header, header_size } = props;
  const sm = header_size ?? 4;
  return (
    <>
      <Row>
        <Col sm={sm}>
          <b>{header}</b>
        </Col>
        <Col>{props.children}</Col>
      </Row>
      <hr />
    </>
  );
};
