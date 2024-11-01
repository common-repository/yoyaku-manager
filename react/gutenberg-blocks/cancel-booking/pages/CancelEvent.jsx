import { frontCancelEventBooking } from "@/api";
import { APIErrorMessage, showSuccessToast } from "@/components/common";
import { BasePage } from "@/gutenberg-blocks/components/layout";
import dt from "@/utils/datetime";
import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Button, Modal } from "react-bootstrap";
import { useNavigate } from "react-router-dom";

export const CancelEvent = () => {
  const url = new URL(window.location.href);
  const token = url.searchParams.get("token");
  const data = window.wpYoyakuCancelData;
  const [errorResponse, setErrorResponse] = useState();

  if (data?.error_message) {
    // キャンセル済みや、キャンセルできない場合
    return (
      <BasePage>
        <p className="text-danger">{data.error_message}</p>
      </BasePage>
    );
  }

  return (
    <BasePage>
      {errorResponse && <APIErrorMessage errorResponse={errorResponse} />}

      <p>{data.event_name}</p>
      <p>
        {`${dt.getWpFormattedDateTimeString(
          data.start_datetime,
        )} ~ ${dt.getWpFormattedDateTimeString(data.end_datetime)}`}
      </p>

      <CancelConfirmBtn token={token} setErrorResponse={setErrorResponse} />
    </BasePage>
  );
};

/**
 * キャンセル確認のモーダル
 * @param token
 * @param setErrorResponse
 * @returns {JSX.Element}
 * @constructor
 */
export const CancelConfirmBtn = ({ token, setErrorResponse }) => {
  const [showModal, setShowModal] = useState(false);
  const handleCloseModal = () => setShowModal(false);
  const handleShowModal = () => setShowModal(true);

  return (
    <>
      <div style={{ textAlign: "center" }}>
        <button
          type="submit"
          className={window.wpYoyakuWrapperAttributes?.class || ""}
          onClick={handleShowModal}
          disabled={showModal}
        >
          {__("Cancel Booking", "yoyaku-manager")}
        </button>
      </div>

      {showModal && (
        <Modal centered show={showModal} onHide={handleCloseModal}>
          <Modal.Header closeButton>
            <Modal.Title>{__("Confirm", "yoyaku-manager")}</Modal.Title>
          </Modal.Header>
          <Modal.Body>
            {__("Do you want to cancel your booking?", "yoyaku-manager")}
          </Modal.Body>
          <Modal.Footer>
            <Button variant="secondary" onClick={handleCloseModal}>
              {__("Close", "yoyaku-manager")}
            </Button>
            <CancelBtn
              token={token}
              handleClose={handleCloseModal}
              setErrorResponse={setErrorResponse}
            />
          </Modal.Footer>
        </Modal>
      )}
    </>
  );
};

/**
 * モーダルのキャンセル実行ボタン
 * @param token
 * @param handleClose
 * @param setIsSubmitting
 * @param setErrorResponse
 * @returns {JSX.Element}
 * @constructor
 */
export const CancelBtn = ({ token, handleClose, setErrorResponse }) => {
  const navigate = useNavigate();
  const [isSubmitting, setIsSubmitting] = useState(false);

  const onClick = async () => {
    setIsSubmitting(true);
    const result = await frontCancelEventBooking({ token: token });
    setIsSubmitting(false);
    handleClose();
    if (result?.is_error) {
      setErrorResponse(result);
    } else {
      showSuccessToast("Canceled.");
      navigate("/completion");
    }
  };

  return (
    <Button variant="primary" onClick={onClick} disabled={isSubmitting}>
      {__("Cancel", "yoyaku-manager")}
    </Button>
  );
};
