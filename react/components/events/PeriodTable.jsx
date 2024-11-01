import { useEventPeriods } from "@/api";
import {
  ActionDivider,
  DetailLink,
  EditLink,
  Pagination,
  TableActionGroup,
} from "@/components/common";
import { H2WithAddBtn } from "@/components/layout";
import { HandleError } from "@/pages/others";
import dt from "@/utils/datetime";
import { settings } from "@/utils/settings";
import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Form, Table } from "react-bootstrap";
import { AddBookingLink, EventPeriodBookingListLink } from "./EventBtn";

/**
 *
 * @param eventId {int}
 * @returns {JSX.Element}
 * @constructor
 */
export const PeriodTable = ({ eventId }) => {
  const [search, setSearch] = useState({
    page: 1,
    per_page: 50,
    event_id: eventId,
    show_past: false,
  });
  const { data, error } = useEventPeriods(search);
  const [selectedId, setSelectedId] = useState();

  // <LoadingData /> を使うとチェックボックスのon/offがバグるため使ってない
  if (error) return <HandleError error={error} />;

  return (
    <div>
      <H2WithAddBtn
        title={__("Periods", "yoyaku-manager")}
        to={`/periods/add/${eventId}`}
      />

      <Form>
        <Form.Check
          label={__("Show Past", "yoyaku-manager")}
          value={search.show_past}
          onChange={() =>
            setSearch({ ...search, show_past: !search.show_past })
          }
        />
      </Form>

      <Table striped hover>
        <thead>
          <tr>
            <th width="50px">ID</th>
            <th width="50%">{__("Period", "yoyaku-manager")}</th>
            <th>{__("Max Ticket Count", "yoyaku-manager")}</th>
            <th>{__("Organizer", "yoyaku-manager")}</th>
          </tr>
        </thead>
        <tbody>
          {data.items.map((item) => (
            <tr
              key={item.id}
              onMouseEnter={() => setSelectedId(item.id)}
              onMouseLeave={() => setSelectedId(null)}
            >
              <td>{item.id}</td>
              <td>
                {dt.getWpFormattedDateTimeString(item.start_datetime)}
                {__(" ~ ", "yoyaku-manager")}
                {dt.getWpFormattedDateTimeString(item.end_datetime)}
                <br />
                <TableActionGroup isVisible={selectedId === item.id}>
                  <DetailLink id={item.id} prefix={"/periods/"} />
                  {settings.canWrite() && (
                    <>
                      <ActionDivider />
                      <EditLink id={item.id} prefix={"/periods/"} />
                    </>
                  )}
                  <ActionDivider />
                  <EventPeriodBookingListLink eventPeriodId={item.id} />
                  {settings.canWrite() && (
                    <>
                      <ActionDivider />
                      <AddBookingLink periodId={item.id} />
                    </>
                  )}
                </TableActionGroup>
              </td>
              <td>{item.max_ticket_count}</td>
              <td>{item.wp_worker}</td>
            </tr>
          ))}
        </tbody>
      </Table>
      <Pagination
        currentPage={search.page}
        numPages={data.num_pages}
        setPage={(page) => setSearch({ ...search, page: page })}
      />
    </div>
  );
};
