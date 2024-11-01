import { updateEventBookingStatusAPI } from "@/api";
import { showErrorToast, showSuccessToast } from "@/components/common";
import { bookingStatus } from "@/utils/consts";
import { settings } from "@/utils/settings";
import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Button, Modal } from "react-bootstrap";

/**
 *
 * @param id
 * @param status
 * @param mutateFn
 * @returns {JSX.Element|null}
 * @constructor
 */
export const UpdateBookingStatusConfirmBtn = ({ id, status, mutateFn }) => {
  if (!settings.canWrite()) return null;

  const [showModal, setShowModal] = useState(false);
  const handleCloseModal = () => setShowModal(false);
  const handleShowModal = () => setShowModal(true);
  let message = "";
  let btnText = "";
  let variant = "outline-secondary";

  if (status === bookingStatus.approved) {
    variant = "outline-success";
    btnText = __("Approve Booking", "yoyaku-manager");
    message = __("Are you sure to approve this booking?", "yoyaku-manager");
  } else if (status === bookingStatus.canceled) {
    variant = "outline-secondary";
    btnText = __("Cancel Booking", "yoyaku-manager");
    message = __("Are you sure to cancel this booking?", "yoyaku-manager");
  } else if (status === bookingStatus.disapproved) {
    variant = "outline-danger";
    btnText = __("Disapprove Booking", "yoyaku-manager");
    message = __("Are you sure to disapprove this booking?", "yoyaku-manager");
  }

  return (
    <>
      <Button variant={variant} onClick={handleShowModal} disabled={showModal}>
        {btnText}
      </Button>
      {showModal && (
        <Modal centered show={showModal} onHide={handleCloseModal}>
          <Modal.Header closeButton>
            <Modal.Title>{__("Confirm", "yoyaku-manager")}</Modal.Title>
          </Modal.Header>
          <Modal.Body>{message}</Modal.Body>
          <Modal.Footer>
            <Button variant="secondary" onClick={handleCloseModal}>
              {__("Close", "yoyaku-manager")}
            </Button>
            <UpdateStatusBtn
              id={id}
              status={status}
              mutateFn={mutateFn}
              handleClose={handleCloseModal}
            />
          </Modal.Footer>
        </Modal>
      )}
    </>
  );
};

/**
 *
 * @param id
 * @param status
 * @param mutateFn
 * @param handleClose
 * @returns {JSX.Element}
 * @constructor
 */
const UpdateStatusBtn = ({ id, status, mutateFn, handleClose }) => {
  let message = "";
  let variant = "secondary";
  if (status === bookingStatus.approved) {
    variant = "success";
    message = __("approve", "yoyaku-manager");
  } else if (status === bookingStatus.canceled) {
    variant = "warning";
    message = __("cancel", "yoyaku-manager");
  } else if (status === bookingStatus.disapproved) {
    variant = "danger";
    message = __("disapprove", "yoyaku-manager");
  }

  const onClick = async () => {
    const result = await updateEventBookingStatusAPI({
      id: id,
      status: status,
    });
    if (result?.is_error) {
      showErrorToast(result.message);
    } else {
      showSuccessToast(result.message);
      mutateFn();
      handleClose();
    }
  };

  return (
    <Button variant={variant} onClick={onClick}>
      {message}
    </Button>
  );
};
