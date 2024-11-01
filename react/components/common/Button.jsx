import { settings } from "@/utils/settings";
import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Button, Spinner, Stack } from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import { showErrorToast, showSuccessToast } from "./Toasts";

export const AddBtn = ({ text = "", to = "/add" }) => {
  const navigate = useNavigate();
  if (!settings.canWrite()) return null;

  return (
    <Button variant="outline-primary" onClick={() => navigate(to)}>
      {text || __("Add", "yoyaku-manager")}
    </Button>
  );
};

/**
 * 保存
 * @param isSubmitting 主に保存に時間がかかる時に利用する
 * @return {JSX.Element|null}
 */
export const SaveBtn = ({ isSubmitting = false }) => (
  <Button variant="primary" type="submit" disabled={isSubmitting}>
    {isSubmitting && (
      <Spinner
        className="me-1"
        as="span"
        animation="border"
        size="sm"
        role="status"
        aria-hidden="true"
      />
    )}
    {__("Save", "yoyaku-manager")}
  </Button>
);

/**
 * 一覧に移動ボタン
 */
export const GoToListPageBtn = ({ text, navigateTo = "/" }) => {
  const navigate = useNavigate();
  return (
    <Button variant="outline-secondary" onClick={() => navigate(navigateTo)}>
      {text}
    </Button>
  );
};

/**
 * 戻るボタン
 */
export const BackBtn = () => {
  const navigate = useNavigate();
  return (
    <Button variant="outline-secondary" onClick={() => navigate(-1)}>
      {__("Back", "yoyaku-manager")}
    </Button>
  );
};

/**
 * モーダルの削除実行ボタン
 * @param id
 * @param deleteAPI
 * @param handleClose
 * @param navigateTo
 * @returns {JSX.Element}
 * @constructor
 */
export const DeleteBtn = ({ id, deleteAPI, handleClose, navigateTo }) => {
  const navigate = useNavigate();
  const [isDeleting, setIsDeleting] = useState(false);
  if (!settings.canDelete()) return null;

  const onClick = async () => {
    setIsDeleting(true);
    const result = await deleteAPI(id);
    setIsDeleting(false);
    handleClose();

    if (result?.is_error) {
      showErrorToast(result.message);
    } else {
      navigate(navigateTo);
      showSuccessToast(__("Deleted.", "yoyaku-manager"));
    }
  };

  return (
    <Button variant="danger" onClick={onClick} disabled={isDeleting}>
      {__("Delete", "yoyaku-manager")}
    </Button>
  );
};

export const BtnGroup = ({ className, children }) => (
  <Stack direction="horizontal" gap={1} className={className}>
    {children}
  </Stack>
);
