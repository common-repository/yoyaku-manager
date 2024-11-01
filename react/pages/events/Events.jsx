import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Table } from "react-bootstrap";
import { useEvents } from "@/api";
import {
  ActionDivider,
  DetailLink,
  EditLink,
  LoadingData,
  Pagination,
  SearchForm,
  SortIconWithNone,
  TableActionGroup,
} from "@/components/common";
import { BaseLicensePage, HeaderWithAdd } from "@/components/layout";
import { changeSortByName } from "@/utils/change-sort";
import { settings } from "@/utils/settings";
import { HandleError } from "@/pages/others";

export const Events = () => {
  const [search, setSearch] = useState({ page: 1, per_page: 50 });
  const { data, error, isLoading } = useEvents(search);
  const [selectedId, setSelectedId] = useState();

  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <HeaderWithAdd title={__("Events", "yoyaku-manager")} />
      <SearchForm
        searchText={search.search}
        setSearchText={(value) =>
          setSearch({ ...search, search: value, page: 1 })
        }
      />

      <Table striped hover>
        <thead>
          <tr>
            <th width="10%">ID</th>
            <th width="90%" onClick={() => setSearch(changeSortByName(search))}>
              {__("Name", "yoyaku-manager")}
              <SortIconWithNone order={search?.order} />
            </th>
          </tr>
        </thead>
        <tbody>
          {data &&
            data.items.map((item) => (
              <tr
                key={item.id}
                onMouseEnter={() => setSelectedId(item.id)}
                onMouseLeave={() => setSelectedId(null)}
              >
                <td>{item.id}</td>
                <td>
                  {item.name}
                  <br />
                  <TableActionGroup isVisible={selectedId === item.id}>
                    <DetailLink id={item.id} />
                    {settings.canWrite() && <ActionDivider />}
                    <EditLink id={item.id} />
                  </TableActionGroup>
                </td>
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
