import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Tab, Tabs } from "react-bootstrap";
import { updateSettings, useSettings } from "@/api";
import {
  APIErrorMessage,
  LoadingData,
  showSuccessToast,
} from "@/components/common";
import { BasePage, Header } from "@/components/layout";
import { HandleError } from "@/pages/others";
import { General, getGeneralSetting } from "./General";
import { getNotificationsSetting, Notifications } from "./Notifications";
import { getPaymentsSetting, Payments } from "./Payments";

export const Settings = () => {
  const { data: allSettings, error, isLoading, mutate } = useSettings();
  const [tabKey, setTabKey] = useState("general");
  const [errorResponse, setErrorResponse] = useState();

  const mutateFn = () => mutate(allSettings);
  const onSubmit = async (data) => {
    const result = await updateSettings(data);
    if (result?.is_error) {
      setErrorResponse(result);
    } else {
      showSuccessToast();
      mutateFn();
    }
  };

  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BasePage>
      <Header title={__("Settings", "yoyaku-manager")} />
      <div className="mt-3">
        <Tabs
          activeKey={tabKey}
          justify
          className="mb-3"
          onSelect={(key) => setTabKey(key)}
        >
          <Tab eventKey="general" title={__("General", "yoyaku-manager")}>
            <General
              values={getGeneralSetting(allSettings)}
              onSubmit={onSubmit}
            />
          </Tab>
          <Tab
            eventKey="notifications"
            title={__("Notifications", "yoyaku-manager")}
          >
            <Notifications
              values={getNotificationsSetting(allSettings)}
              onSubmit={onSubmit}
            />
          </Tab>
          <Tab eventKey="payments" title={__("Payments", "yoyaku-manager")}>
            <Payments
              values={getPaymentsSetting(allSettings)}
              onSubmit={onSubmit}
            />
          </Tab>
        </Tabs>

        {errorResponse && <APIErrorMessage errorResponse={errorResponse} />}
      </div>
    </BasePage>
  );
};
