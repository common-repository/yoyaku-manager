import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Button, Card, Modal, Row } from "react-bootstrap";
import { useParams } from "react-router-dom";
import {
  deleteEventPeriodAPI,
  deleteGoogleMeetAPI,
  deleteZoomMeetingAPI,
  useEventPeriod,
} from "@/api";
import {
  ActionDivider,
  APIErrorMessage,
  CardRow,
  DeleteConfirmLink,
  EditLink,
  GoToListPageBtn,
  LoadingData,
  showSuccessToast,
  TableActionGroup,
} from "@/components/common";
import { BaseLicensePage, Header } from "@/components/layout";
import dt from "@/utils/datetime";
import { settings } from "@/utils/settings";
import { HandleError } from "@/pages/others";

export const EventPeriodDetail = () => {
  const [errorResponse, setErrorResponse] = useState();
  const params = useParams();
  const { data, error, isLoading, mutate } = useEventPeriod(params.id);

  const mutateFn = () => {
    mutate(data);
    setErrorResponse(null);
  };

  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <Header title={__("Period Detail", "yoyaku-manager")}>
        <GoToListPageBtn
          text={__("Period List", "yoyaku-manager")}
          navigateTo={`/${data.event_id}`}
        />
      </Header>

      {errorResponse && <APIErrorMessage errorResponse={errorResponse} />}

      <Row className="detail-card-wrap">
        <Card>
          <Card.Body>
            <CardRow header="ID">{data.id}</CardRow>

            <CardRow header={__("DateTime", "yoyaku-manager")}>
              {dt.getWpFormattedDateTimeString(data.start_datetime)}
              {__(" ~ ", "yoyaku-manager")}
              {dt.getWpFormattedDateTimeString(data.end_datetime)}
            </CardRow>

            <CardRow header={__("Max Ticket Count", "yoyaku-manager")}>
              {data.max_ticket_count}
            </CardRow>

            <CardRow header={__("Sold Tickets Count", "yoyaku-manager")}>
              {data.tickets.map((ticket) => (
                <p key={ticket.id} className={"m-0"}>
                  <span>{ticket.name}</span>
                  <span className="mx-2">×</span>
                  <span>{ticket.sold_ticket_count}</span>
                </p>
              ))}
            </CardRow>

            <CardRow header={__("Organizer", "yoyaku-manager")}>
              {data.wp_worker}
            </CardRow>

            <CardRow header={__("Location", "yoyaku-manager")}>
              {data.location}
            </CardRow>

            <CardRow header={__("Online Meeting URL", "yoyaku-manager")}>
              {data.online_meeting_url}
            </CardRow>

            {settings.zoom_is_active && (
              <CardRow header={__("Zoom Meeting", "yoyaku-manager")}>
                {data.zoom_start_url && data.zoom_join_url && (
                  <>
                    <a href={data.zoom_start_url} className="me-3">
                      {__("Start URL (Host)", "yoyaku-manager")}
                    </a>
                    <a href={data.zoom_join_url}>
                      {__("Join URL (Participants)", "yoyaku-manager")}
                    </a>
                  </>
                )}
              </CardRow>
            )}
            {settings.google_calendar && (
              <CardRow header={__("Google Meet", "yoyaku-manager")}>
                {data.google_meet_url && (
                  <a href={data.google_meet_url}>
                    {__("Google Meet URL", "yoyaku-manager")}
                  </a>
                )}
              </CardRow>
            )}

            <TableActionGroup isVisible>
              <EditLink id={data.id} prefix={"/periods/"} />
              {settings.canWrite() && settings.canDelete() && <ActionDivider />}
              <DeleteConfirmLink
                id={data.id}
                deleteAPI={deleteEventPeriodAPI}
                navigateTo={`/${data.event_id}`}
              />
              {data.zoom_start_url &&
                data.zoom_join_url &&
                settings.canWrite() && (
                  <>
                    <ActionDivider />
                    <DeleteMeetingConfirmLink
                      id={data.id}
                      deleteAPI={deleteZoomMeetingAPI}
                      mutateFn={mutateFn}
                      setErrorResponse={setErrorResponse}
                      text={__("Delete Zoom Meeting", "yoyaku-manager")}
                    />
                  </>
                )}
              {data.google_calendar_event_id && settings.canWrite() && (
                <>
                  <ActionDivider />
                  <DeleteMeetingConfirmLink
                    id={data.id}
                    deleteAPI={deleteGoogleMeetAPI}
                    mutateFn={mutateFn}
                    setErrorResponse={setErrorResponse}
                    text={__("Delete Google Meet & Calendar", "yoyaku-manager")}
                  />
                </>
              )}
            </TableActionGroup>
          </Card.Body>
        </Card>
      </Row>
    </BaseLicensePage>
  );
};

/**
 * 削除確認のモーダル
 * @param id
 * @param deleteAPI
 * @param mutateFn
 * @param setErrorResponse
 * @param text
 * @constructor
 */
export const DeleteMeetingConfirmLink = ({
  id,
  deleteAPI,
  mutateFn,
  setErrorResponse,
  text,
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
            <DeleteMeetingBtn
              id={id}
              deleteAPI={deleteAPI}
              handleClose={handleCloseModal}
              mutateFn={mutateFn}
              setErrorResponse={setErrorResponse}
            />
          </Modal.Footer>
        </Modal>
      )}
    </>
  );
};

/**
 * モーダルの削除実行ボタン
 * @param id
 * @param deleteAPI
 * @param handleClose
 * @param mutateFn
 * @param setErrorResponse
 */
export const DeleteMeetingBtn = ({
  id,
  deleteAPI,
  handleClose,
  mutateFn,
  setErrorResponse,
}) => {
  const [isDeleting, setIsDeleting] = useState(false);
  if (!settings.canDelete()) return null;

  const onClick = async () => {
    setIsDeleting(true);
    const result = await deleteAPI(id);
    setIsDeleting(false);
    handleClose();

    if (result?.is_error) {
      setErrorResponse(result);
    } else {
      showSuccessToast();
      mutateFn();
    }
  };

  return (
    <Button variant="danger" onClick={onClick} disabled={isDeleting}>
      {__("Delete", "yoyaku-manager")}
    </Button>
  );
};
