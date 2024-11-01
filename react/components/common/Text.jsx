import { settings } from "@/utils/settings";
import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Button, Modal } from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import { DeleteBtn } from "./Button";

export const DetailLink = ({ id, prefix = "/" }) => {
  const navigate = useNavigate();
  return (
    <Button
      variant="link"
      className="p-0"
      onClick={() => navigate(`${prefix}${id}`)}
    >
      {__("Details", "yoyaku-manager")}
    </Button>
  );
};

export const EditLink = ({ id, prefix = "/", from }) => {
  const navigate = useNavigate();
  const to = from
    ? `${prefix}${id}/update?from=${from}`
    : `${prefix}${id}/update`;

  if (!settings.canWrite()) return null;

  return (
    <Button variant="link" className="p-0" onClick={() => navigate(to)}>
      {__("edit", "yoyaku-manager")}
    </Button>
  );
};

/**
 * 削除確認のモーダル
 * @param id
 * @param deleteAPI
 * @param navigateTo
 * @param text
 * @returns {JSX.Element}
 * @constructor
 */
export const DeleteConfirmLink = ({
  id,
  deleteAPI,
  navigateTo,
  text = __("Delete", "yoyaku-manager"),
}) => {
  const [showModal, setShowModal] = useState(false);
  const handleCloseModal = () => setShowModal(false);
  const handleShowModal = () => setShowModal(true);

  if (!settings.canDelete()) return null;

  return (
    <>
      <Button
        variant="link"
        className="link-danger p-0"
        onClick={handleShowModal}
        disabled={showModal}
      >
        <span className="delete-link">{text}</span>
      </Button>

      {showModal && (
        <Modal centered show={showModal} onHide={handleCloseModal}>
          <Modal.Header closeButton>
            <Modal.Title>{__("Confirm", "yoyaku-manager")}</Modal.Title>
          </Modal.Header>
          <Modal.Body>
            {__("Are you sure to delete?", "yoyaku-manager")}
          </Modal.Body>
          <Modal.Footer>
            <Button variant="secondary" onClick={handleCloseModal}>
              {__("Close", "yoyaku-manager")}
            </Button>
            <DeleteBtn
              id={id}
              deleteAPI={deleteAPI}
              handleClose={handleCloseModal}
              navigateTo={navigateTo}
            />
          </Modal.Footer>
        </Modal>
      )}
    </>
  );
};

export const RequiredLabel = () => (
  <span className="ms-1 text-danger">{__("(required)", "yoyaku-manager")}</span>
);
