import { FormHeader } from "@/gutenberg-blocks/components/event";
import { BasePage } from "@/gutenberg-blocks/components/layout";
import dt from "@/utils/datetime";
import { __ } from "@wordpress/i18n";
import { Button, Col, Row } from "react-bootstrap";
import { useNavigate } from "react-router-dom";

export const SelectPeriod = ({ eventData }) => {
  if (!eventData) {
    return <p>{__("There are no events.", "yoyaku-manager")}</p>;
  }

  const navigate = useNavigate();

  return (
    <BasePage>
      <FormHeader order={1} />

      <p>{__("Please select date and time.", "yoyaku-manager")}</p>

      <div className="d-grid gap-2">
        {eventData.periods.map((period) => (
          <>
            <Button
              variant="outline-primary"
              disabled={period.rest_ticket_count <= 0}
              onClick={() => navigate(`/${period.uuid}`)}
              className="p-3"
            >
              {getLabel(
                period.start_datetime,
                period.end_datetime,
                period.wp_worker,
                period.rest_ticket_count,
              )}
            </Button>
          </>
        ))}
      </div>
    </BasePage>
  );
};

/**
 * 開催日の相違により、表示を切り分ける
 * @param startDatetime
 * @param endDatetime
 * @param workerName
 * @param restTicketCount
 */
const getLabel = (startDatetime, endDatetime, workerName, restTicketCount) => {
  const isSameDate = startDatetime.slice(0, 10) === endDatetime.slice(0, 10);
  let periodText = null;
  let colSize = 6;
  if (isSameDate) {
    const date = dt.getWpFormattedDateString(startDatetime);
    const startTime = dt.getWpFormattedTimeString(startDatetime);
    const endTime = dt.getWpFormattedTimeString(endDatetime);
    colSize = 6;
    periodText = `${date} ${startTime} ~ ${endTime}`;
  } else {
    const startDt = dt.getWpFormattedDateTimeString(startDatetime);
    const endDt = dt.getWpFormattedDateTimeString(endDatetime);
    colSize = 8;
    periodText = `${startDt} ~ ${endDt}`;
  }

  return (
    <div className="front-select-period">
      <Row>
        <Col sm={colSize}>{periodText}</Col>
        <Col>
          {0 < restTicketCount ? (
            <>
              {sprintf(
                /* translators: %d is restTicketCount */
                __("%d left", "yoyaku-manager"),
                restTicketCount,
              )}
            </>
          ) : (
            <span className="text-danger">{__("Full", "yoyaku-manager")}</span>
          )}
        </Col>
      </Row>
      {workerName && (
        <Row className="mb-0">
          <Col sm={colSize}>{workerName}</Col>
        </Row>
      )}
    </div>
  );
};
