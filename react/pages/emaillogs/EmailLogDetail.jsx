import { deleteEmailLogAPI, sendUndeliveredEmailAPI, useEmailLog } from "@/api";
import {
  CardRow,
  DeleteConfirmLink,
  LoadingData,
  SentStatusIcon,
  showErrorToast,
  showSuccessToast,
  TableActionGroup,
} from "@/components/common";
import { BaseLicensePage, Header } from "@/components/layout";
import { HandleError } from "@/pages/others";
import dt from "@/utils/datetime";
import { settings } from "@/utils/settings";
import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Button, Card, Row, Spinner } from "react-bootstrap";
import { useParams } from "react-router-dom";

export const EmailLogDetail = () => {
  const params = useParams();
  const { data, error, isLoading, mutate } = useEmailLog(params.id);
  const [isSending, setIsSending] = useState(false);

  const resend = async (id) => {
    setIsSending(true);
    const result = await sendUndeliveredEmailAPI({ id: id });
    setIsSending(false);
    if (result?.is_error) {
      showErrorToast(result.message);
    } else {
      await mutate(data);
      showSuccessToast(__("Send successfully.", "yoyaku-manager"));
    }
  };

  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <Header title={__("Email Log Detail", "yoyaku-manager")}>
        {settings.canWrite() && !data.sent && (
          <Button
            variant="outline-primary"
            onClick={() => resend(data.id)}
            disabled={isSending}
          >
            {isSending ? (
              <Spinner as="span" size="sm" animation="border" />
            ) : (
              __("Resend", "yoyaku-manager")
            )}
          </Button>
        )}
      </Header>
      <Row className="detail-card-wrap">
        <Card>
          <Card.Body>
            <CardRow header_size={3} header={__("Date Sent", "yoyaku-manager")}>
              <SentStatusIcon status={data.sent} />
              {dt.getWpFormattedDateTimeString(data.sent_datetime)}
            </CardRow>
            <CardRow header_size={3} header={__("To", "yoyaku-manager")}>
              {data.to}
            </CardRow>
            <CardRow header_size={3} header={__("Subject", "yoyaku-manager")}>
              {data.subject}
            </CardRow>
            <CardRow header_size={3} header={__("Message", "yoyaku-manager")}>
              <div style={{ whiteSpace: "pre-line" }}>{data.content}</div>
            </CardRow>

            <TableActionGroup isVisible>
              <DeleteConfirmLink
                id={data.id}
                deleteAPI={deleteEmailLogAPI}
                navigateTo="/"
              />
            </TableActionGroup>
          </Card.Body>
        </Card>
      </Row>
    </BaseLicensePage>
  );
};
