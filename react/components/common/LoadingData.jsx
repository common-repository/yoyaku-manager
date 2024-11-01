import {Spinner} from "react-bootstrap";

/**
 *
 * @returns {JSX.Element}
 * @constructor
 */
export const LoadingData = () => (
  <div className="mt-3 d-flex align-items-center justify-content-center">
    <Spinner variant="primary" as="span" animation="grow" />
    {/*<strong className="ms-2">Loading...</strong>*/}
  </div>
);
