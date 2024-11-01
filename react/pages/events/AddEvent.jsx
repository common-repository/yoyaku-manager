import { __ } from "@wordpress/i18n";
import { addEventAPI } from "@/api";
import { EventForm } from "@/components/events";
import { BaseLicensePage, Header } from "@/components/layout";
import { settings } from "@/utils/settings";
import { Forbidden } from "@/pages/others";

export const AddEvent = () => {
  const defaultValues = {
    name: "",
    use_approval_system: false,
    show_worker: false,
    is_online_payment: false,
    min_time_to_close_booking: 0,
    min_time_to_cancel_booking: 0,
    max_tickets_per_booking: 1,
    notification_ids: [],
  };

  if (!settings.canWrite()) return <Forbidden />;

  return (
    <BaseLicensePage>
      <Header title={__("Add Event", "yoyaku-manager")} />
      <EventForm
        defaultValues={defaultValues}
        dataHandler={addEventAPI}
        navigateTo={"/"}
      />
    </BaseLicensePage>
  );
};
