import { updateCustomerAPI, useCustomer } from "@/api";
import { LoadingData } from "@/components/common";
import { CustomerForm } from "@/components/customers";
import { BaseLicensePage, Header } from "@/components/layout";
import { Forbidden, HandleError } from "@/pages/others";
import { settings } from "@/utils/settings";
import { __ } from "@wordpress/i18n";
import { useParams, useSearchParams } from "react-router-dom";

export const UpdateCustomer = () => {
  const [searchParams, setSearchParams] = useSearchParams();
  const params = useParams();
  const { data, error, isLoading } = useCustomer(params.id);
  const navigateTo =
    searchParams.get("from") === "detail" ? `/${params.id}` : "/";

  if (!settings.canWrite()) return <Forbidden />;
  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <Header title={__("Edit Customer", "yoyaku-manager")} />
      <CustomerForm
        defaultValues={data}
        dataHandler={updateCustomerAPI}
        navigateTo={navigateTo}
      />
    </BaseLicensePage>
  );
};
