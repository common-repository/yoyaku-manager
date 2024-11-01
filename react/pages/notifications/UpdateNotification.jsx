import { __ } from "@wordpress/i18n";
import { useParams } from "react-router-dom";
import { updateNotificationAPI, useNotification } from "@/api";
import { LoadingData } from "@/components/common";
import { BaseLicensePage, Header } from "@/components/layout";
import { NotificationForm } from "@/components/notifications";
import { settings } from "@/utils/settings";
import { Forbidden, HandleError } from "@/pages/others";

export const UpdateNotification = () => {
  const params = useParams();
  const { data, error, isLoading } = useNotification(params.id);

  if (!settings.canWrite()) return <Forbidden />;
  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <Header
        title={
          /* translators: %s is notification name */
          sprintf(__("Edit %s", "yoyaku-manager"), data.name)
        }
      />
      <NotificationForm
        defaultValues={data}
        dataHandler={updateNotificationAPI}
      />
    </BaseLicensePage>
  );
};
