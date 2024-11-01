import { __ } from "@wordpress/i18n";
import { useParams } from "react-router-dom";
import { addEventPeriodAPI } from "@/api";
import { PeriodForm } from "@/components/events";
import { BaseLicensePage, Header } from "@/components/layout";
import { settings } from "@/utils/settings";
import { Forbidden } from "@/pages/others";

export const AddEventPeriod = () => {
  const params = useParams();
  const defaultValues = {
    event_id: parseInt(params.eventId),
    location: "",
    online_meeting_url: "",
  };

  if (!settings.canWrite()) return <Forbidden />;

  return (
    <BaseLicensePage>
      <Header title={__("Add Period", "yoyaku-manager")} />
      <PeriodForm
        defaultValues={defaultValues}
        dataHandler={addEventPeriodAPI}
        navigateTo={`/${params.eventId}`}
      />
    </BaseLicensePage>
  );
};
