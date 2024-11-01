import { __ } from "@wordpress/i18n";
import { useParams, useSearchParams } from "react-router-dom";
import { updateEventAPI, useEvent } from "@/api";
import { LoadingData } from "@/components/common";
import { EventForm } from "@/components/events";
import { BaseLicensePage, Header } from "@/components/layout";
import { settings } from "@/utils/settings";
import { Forbidden, HandleError } from "@/pages/others";

export const UpdateEvent = () => {
  const [searchParams, setSearchParams] = useSearchParams();
  const params = useParams();
  const { data, error, isLoading } = useEvent(params.id);
  const navigateTo =
    searchParams.get("from") === "detail" ? `/${params.id}` : "/";

  const getDefaultValues = (data) => {
    if (data) {
      let copied = { ...data };
      copied.notification_ids = data.notifications.map((item) => item.id);
      // 分から時に変換
      copied.min_time_to_close_booking = Math.trunc(
        copied.min_time_to_close_booking / 60,
      );
      copied.min_time_to_cancel_booking = Math.trunc(
        copied.min_time_to_cancel_booking / 60,
      );
      delete copied.notifications;
      return copied;
    } else {
      return null;
    }
  };

  if (!settings.canWrite()) return <Forbidden />;
  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <Header title={__("Edit Event", "yoyaku-manager")} />
      <EventForm
        defaultValues={getDefaultValues(data)}
        dataHandler={updateEventAPI}
        navigateTo={navigateTo}
      />
    </BaseLicensePage>
  );
};
