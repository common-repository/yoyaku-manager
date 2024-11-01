import { __ } from "@wordpress/i18n";
import { useParams } from "react-router-dom";
import { updateEventPeriodAPI, useEventPeriod } from "@/api";
import { LoadingData } from "@/components/common";
import { PeriodForm } from "@/components/events";
import { BaseLicensePage, Header } from "@/components/layout";
import { settings } from "@/utils/settings";
import { Forbidden, HandleError } from "@/pages/others";

export const UpdateEventPeriod = () => {
  const params = useParams();
  const { data, error, isLoading } = useEventPeriod(params.id);

  if (!settings.canWrite()) return <Forbidden />;
  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  // <input type='datetime-local'> の初期値の書式（YYYY-MM-DDThh:mm）に変換する
  const parseData = (data) => {
    data.start_datetime = data.start_datetime.substring(0, 16);
    data.end_datetime = data.end_datetime.substring(0, 16);
    return data;
  };

  return (
    <BaseLicensePage>
      <Header title={__("Edit Period", "yoyaku-manager")} />
      <PeriodForm
        defaultValues={parseData(data)}
        dataHandler={updateEventPeriodAPI}
        navigateTo={`/periods/${params.id}`}
      />
    </BaseLicensePage>
  );
};
