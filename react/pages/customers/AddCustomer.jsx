import { addCustomerAPI } from "@/api";
import { CustomerForm } from "@/components/customers";
import { BaseLicensePage, Header } from "@/components/layout";
import { Forbidden } from "@/pages/others";
import { settings } from "@/utils/settings";
import { __ } from "@wordpress/i18n";

export const AddCustomer = () => {
  const defaultValues = {
    id: null,
    first_name: "",
    last_name: "",
    email: "",
    birthday: null,
    gender: null,
    memo: "",
  };

  if (!settings.canWrite()) return <Forbidden />;

  return (
    <BaseLicensePage>
      <Header title={__("Add Customer", "yoyaku-manager")} />
      <CustomerForm
        defaultValues={defaultValues}
        dataHandler={addCustomerAPI}
        navigateTo="/"
      />
    </BaseLicensePage>
  );
};
