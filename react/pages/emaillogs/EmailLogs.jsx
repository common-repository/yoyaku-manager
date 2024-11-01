import { sendUndeliveredEmailAPI, useEmailLogs } from "@/api";
import {
  ActionDivider,
  DetailLink,
  LoadingData,
  Pagination,
  RecordsInfo,
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
import { Button, Spinner, Table } from "react-bootstrap";
import { useSearchParams } from "react-router-dom";

export const EmailLogs = () => {
  const [searchParams, setSearchParams] = useSearchParams();
  const [search, setSearch] = useState({ page: 1, per_page: 100 });
  const { data, error, isLoading, mutate } = useEmailLogs(search);
  const [selectedId, setSelectedId] = useState();
  const [isSending, setIsSending] = useState(false);

  const sendFailedEmail = async () => {
    setIsSending(true);
    const result = await sendUndeliveredEmailAPI();
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
      <Header title={__("Email Logs", "yoyaku-manager")}>
        {settings.canWrite() && !!data?.failed_count && (
          <Button
            variant="outline-primary"
            onClick={sendFailedEmail}
            disabled={isSending}
          >
            {isSending ? (
              <Spinner as="span" size="sm" animation="border" />
            ) : (
              __("Send Failed Emails", "yoyaku-manager")
            )}
          </Button>
        )}
      </Header>

      <RecordsInfo>
        <Button
          variant="link"
          className="p-0"
          onClick={() => {
            setSearchParams();
            let copied = { ...search };
            delete copied["sent"];
            setSearch(copied);
          }}
        >
          {__("All", "yoyaku-manager")}
          <span className="count">{` (${data.all_count})`}</span>
        </Button>
        <ActionDivider />
        <Button
          variant="link"
          className="p-0"
          onClick={() => {
            setSearchParams({ sent: false });
            setSearch({ ...search, sent: false });
          }}
        >
          {__("Failed Emails", "yoyaku-manager")}
          <span className="count">{` (${data.failed_count})`}</span>
        </Button>
      </RecordsInfo>

      <Table striped hover>
        <thead>
          <tr>
            <th width="25%">{__("Date Sent", "yoyaku-manager")}</th>
            <th width="50%">{__("Subject", "yoyaku-manager")}</th>
            <th>{__("To", "yoyaku-manager")}</th>
          </tr>
        </thead>
        <tbody>
          {data.items.map((item) => (
            <tr
              key={item.id}
              onMouseEnter={() => setSelectedId(item.id)}
              onMouseLeave={() => setSelectedId(null)}
            >
              <td>
                <SentStatusIcon status={item.sent} />
                {dt.getWpFormattedDateTimeString(item.sent_datetime)}
                <TableActionGroup isVisible={selectedId === item.id}>
                  <DetailLink id={item.id} />
                </TableActionGroup>
              </td>
              <td>{item.subject}</td>
              <td>{item.to}</td>
            </tr>
          ))}
        </tbody>
      </Table>

      <Pagination
        currentPage={search.page}
        numPages={data.num_pages}
        setPage={(page) => setSearch({ ...search, page: page })}
      />
    </BaseLicensePage>
  );
};
