import { useState } from "@wordpress/element";
import { __, _n, sprintf } from "@wordpress/i18n";
import { Accordion, Col, Form, Row } from "react-bootstrap";
import { deleteNotificationAPI, useNotifications } from "@/api";
import {
  ActionDivider,
  DeleteConfirmLink,
  EditLink,
  LoadingData,
  TableActionGroup,
  TimingBadge,
} from "@/components/common";
import { BaseLicensePage, HeaderWithAdd } from "@/components/layout";
import { notificationTiming } from "@/utils/consts";
import dt from "@/utils/datetime";
import { settings } from "@/utils/settings";
import { HandleError } from "@/pages/others";

export const Notifications = () => {
  const [searchParams, setSearchParams] = useState({
    orderby: "timing",
    order: "asc",
  });
  const { data, error, isLoading } = useNotifications(searchParams);

  const onChangeSort = (value) => {
    const params = value.split("__");
    if (params.length === 2) {
      setSearchParams({
        ...searchParams,
        orderby: params[0],
        order: params[1],
      });
    }
  };

  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <HeaderWithAdd title={__("Notifications", "yoyaku-manager")} />

      <Row xs="auto" className="justify-content-end mb-2">
        <Col>
          <Form.Label className="col-form-label">
            {__("Sort", "yoyaku-manager")}
          </Form.Label>
        </Col>
        <Col className="y-sort-wrap ps-0">
          <Form.Select
            label={__("Sort", "yoyaku-manager")}
            value={`${searchParams.orderby}__${searchParams.order}`}
            onChange={(e) => onChangeSort(e.target.value)}
          >
            <option value="timing__asc">
              {__("Timing", "yoyaku-manager")}
            </option>
            <option value="name__asc">
              {__("Name(asc)", "yoyaku-manager")}
            </option>
            <option value="name__desc">
              {__("Name(desc)", "yoyaku-manager")}
            </option>
          </Form.Select>
        </Col>
      </Row>

      <Accordion>
        {data?.items.map((item) => (
          <NotificationRow key={item.id} item={item} />
        ))}
      </Accordion>
    </BaseLicensePage>
  );
};

const NotificationRow = ({ item }) => {
  const days_text = item.is_before
    ? sprintf(
        /* translators: %d is replaced with "number" */
        _n("%dDay Before", "%dDays Before.", item.days, "yoyaku-manager"),
        item.days,
      )
    : sprintf(
        /* translators: %d is replaced with "number" */
        _n("%dDay After", "%dDays After.", item.days, "yoyaku-manager"),
        item.days,
      );
  const name =
    item.timing === notificationTiming.scheduled
      ? `${item.name} ${days_text} ${dt.formatTimeRemoveSecond(item.time)}`
      : item.name;

  return (
    <Accordion.Item eventKey={`${item.id}`}>
      <Accordion.Header>
        <TimingBadge timing={item.timing} name={name} />
      </Accordion.Header>
      <Accordion.Body>
        <p>
          <b>{item.subject}</b>
        </p>
        <div style={{ whiteSpace: "pre-line" }}>{item.content}</div>
        <hr />
        <TableActionGroup isVisible>
          <EditLink id={item.id} />
          {settings.canWrite() && settings.canDelete() && <ActionDivider />}
          {/* 一覧画面のため navigateTo="/" では画面更新されない。0でリロードする */}
          <DeleteConfirmLink
            id={item.id}
            deleteAPI={deleteNotificationAPI}
            navigateTo={0}
          />
        </TableActionGroup>
      </Accordion.Body>
    </Accordion.Item>
  );
};
