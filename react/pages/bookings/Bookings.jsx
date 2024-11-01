import { useEventBookings } from "@/api";
import {
  ActionDivider,
  BookingStatusBadge,
  DetailLink,
  EditLink,
  LoadingData,
  Pagination,
  PaymentStatusBadge,
  SearchForm,
  TableActionGroup,
} from "@/components/common";
import { BaseLicensePage, Header } from "@/components/layout";
import { HandleError } from "@/pages/others";
import dt from "@/utils/datetime";
import { getFormattedPrice } from "@/utils/price";
import { settings } from "@/utils/settings";
import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Table } from "react-bootstrap";
import { useSearchParams } from "react-router-dom";

export const Bookings = () => {
  const [searchParams, setSearchParams] = useSearchParams();
  const searchEventId = searchParams.get("eventId");
  const searchPeriodId = searchParams.get("eventPeriodId");
  const [search, setSearch] = useState({
    page: 1,
    per_page: 100,
    event_id: searchEventId,
    event_period_id: searchPeriodId,
  });
  const { data, error, isLoading } = useEventBookings(search);
  const [selectedId, setSelectedId] = useState();

  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <Header title={__("Bookings", "yoyaku-manager")} />

      <SearchForm
        searchText={search.search}
        setSearchText={(value) => setSearch({ ...search, search: value })}
      />
      <FilteringText eventId={searchEventId} periodId={searchPeriodId} />

      <Table striped hover>
        <thead>
          <tr>
            <th width="20%">{__("Event Name / Start", "yoyaku-manager")}</th>
            <th>{__("Customer", "yoyaku-manager")}</th>
            <th>{__("Payment", "yoyaku-manager")}</th>
            <th>{__("Memo", "yoyaku-manager")}</th>
          </tr>
        </thead>
        <tbody>
          {data.items &&
            data.items.map((item) => (
              <tr
                key={item.id}
                onMouseEnter={() => setSelectedId(item.id)}
                onMouseLeave={() => setSelectedId(null)}
              >
                <td>
                  {item.event_name}
                  <br />
                  {dt.getWpFormattedDateTimeString(item.period_start_datetime)}
                  <TableActionGroup isVisible={selectedId === item.id}>
                    <DetailLink id={item.id} />
                    {settings.canWrite() && <ActionDivider />}
                    <EditLink id={item.id} />
                  </TableActionGroup>
                </td>
                <td>
                  <BookingStatusBadge status={item.status} />
                  <br />
                  {`${item.first_name} ${item.last_name}`}
                  <br />
                  {item.email}
                </td>
                <td>
                  <PaymentStatusBadge status={item.payment_status} />
                  <br />
                  {getFormattedPrice(item.amount)}
                </td>
                <td style={{ wordBreak: "break-all" }}>{item.memo}</td>
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

/**
 * イベントIDか、イベント期間IDで絞り込みしているときに表示するテキスト
 * @param eventId
 * @param periodId
 * @returns {JSX.Element|null}
 */
const FilteringText = ({ eventId, periodId }) => {
  if (!eventId && !periodId) {
    return null;
  } else {
    return (
      <p className=" pb-1 m-0">
        <span className="me-2">{__("[Filter]", "yoyaku-manager")}</span>
        {eventId && __("Event ID = ", "yoyaku-manager") + eventId}
        {periodId && __("Period ID = ", "yoyaku-manager") + periodId}
      </p>
    );
  }
};
