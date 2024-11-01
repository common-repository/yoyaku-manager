import { deleteCustomerAPI, useCustomer } from "@/api";
import {
  ActionDivider,
  CardRow,
  DeleteConfirmLink,
  EditLink,
  LoadingData,
  TableActionGroup,
} from "@/components/common";
import { BaseLicensePage, Header } from "@/components/layout";
import { HandleError } from "@/pages/others";
import dt from "@/utils/datetime";
import label from "@/utils/labels";
import { settings } from "@/utils/settings";
import { __ } from "@wordpress/i18n";
import { Card, Row } from "react-bootstrap";
import { useParams } from "react-router-dom";

export const CustomerDetail = () => {
  const params = useParams();
  const { data, error, isLoading } = useCustomer(params.id);
  const optionFields = settings.getOptionFieldSettings();

  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <Header title={__("Customer Detail", "yoyaku-manager")} />
      <Row className="detail-card-wrap">
        <Card>
          <Card.Body>
            <CardRow header={__("Name", "yoyaku-manager")}>
              {`${data.first_name} ${data.last_name}`}
            </CardRow>

            {!optionFields.rubyIsHidden && (
              <CardRow header={__("Ruby", "yoyaku-manager")}>
                {`${data.first_name_ruby} ${data.last_name_ruby}`}
              </CardRow>
            )}

            <CardRow header={__("Email", "yoyaku-manager")}>
              {data.email}
            </CardRow>

            {!optionFields.phoneIsHidden && (
              <CardRow header={__("Phone", "yoyaku-manager")}>
                {data.phone}
              </CardRow>
            )}

            {!optionFields.birthdayIsHidden && (
              <CardRow header={__("Birthday", "yoyaku-manager")}>
                {data.birthday}
              </CardRow>
            )}

            {!optionFields.zipcodeIsHidden && (
              <CardRow header={__("Zipcode", "yoyaku-manager")}>
                {data.zipcode}
              </CardRow>
            )}

            {!optionFields.addressIsHidden && (
              <CardRow header={__("Address", "yoyaku-manager")}>
                {data.address}
              </CardRow>
            )}

            {!optionFields.genderIsHidden && (
              <CardRow header={__("Gender", "yoyaku-manager")}>
                {label.getGenderLabel(data.gender)}
              </CardRow>
            )}

            <CardRow header={__("Memo", "yoyaku-manager")}>
              <div style={{ whiteSpace: "pre-line" }}>{data.memo}</div>
            </CardRow>

            <CardRow header={__("Registered", "yoyaku-manager")}>
              {dt.getWpFormattedDateTimeString(data.registered)}
            </CardRow>

            <TableActionGroup isVisible>
              <EditLink id={data.id} from="detail" />
              {settings.canWrite() && settings.canDelete() && <ActionDivider />}
              <DeleteConfirmLink
                id={data.id}
                deleteAPI={deleteCustomerAPI}
                navigateTo="/"
              />
            </TableActionGroup>
          </Card.Body>
        </Card>
      </Row>
    </BaseLicensePage>
  );
};
