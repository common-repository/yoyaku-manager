import { useCustomers } from "@/api";
import {
  ActionDivider,
  AddBtn,
  DetailLink,
  EditLink,
  LoadingData,
  Pagination,
  SearchForm,
  SortIconWithNone,
  TableActionGroup,
} from "@/components/common";
import { BaseLicensePage, Header } from "@/components/layout";
import { HandleError } from "@/pages/others";
import { changeSortByName } from "@/utils/change-sort";
import { settings } from "@/utils/settings";
import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Table } from "react-bootstrap";

export const Customers = () => {
  const [search, setSearch] = useState({ page: 1, per_page: 100 });
  const { data, error, isLoading } = useCustomers(search);
  const [selectedId, setSelectedId] = useState();

  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <Header title={__("Customers", "yoyaku-manager")}>
        <AddBtn />
      </Header>
      <SearchForm
        searchText={search.search}
        setSearchText={(value) =>
          setSearch({ ...search, search: value, page: 1 })
        }
      />

      <Table striped hover>
        <thead>
          <tr>
            <th width="33%" onClick={() => setSearch(changeSortByName(search))}>
              {__("Name", "yoyaku-manager")}
              <SortIconWithNone order={search?.order} />
            </th>
            <th width="34%">{__("Email", "yoyaku-manager")}</th>
            <th>{__("Phone", "yoyaku-manager")}</th>
          </tr>
        </thead>
        <tbody>
          {data.items.map((customer) => (
            <tr
              key={customer.id}
              onMouseEnter={() => setSelectedId(customer.id)}
              onMouseLeave={() => setSelectedId(null)}
            >
              <td>
                {`${customer.first_name} ${customer.last_name}`}
                <TableActionGroup isVisible={selectedId === customer.id}>
                  <DetailLink id={customer.id} />
                  {settings.canWrite() && <ActionDivider />}
                  <EditLink id={customer.id} />
                </TableActionGroup>
              </td>
              <td>{customer.email}</td>
              <td>{customer.phone}</td>
            </tr>
          ))}
        </tbody>
      </Table>

      <Pagination
        currentPage={search.page}
        numPages={data.num_pages}
        setPage={(page) => setSearch({ ...search, page: page })}
      />
    </BaseLicensePage>
  );
};
