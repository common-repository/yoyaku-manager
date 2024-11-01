import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { Table } from "react-bootstrap";
import { useWorkers } from "@/api";
import {
  LoadingData,
  Pagination,
  SearchForm,
  SortIcon,
} from "@/components/common";
import { BaseLicensePage, Header } from "@/components/layout";
import { HandleError } from "@/pages/others";

/**
 * wpのユーザー一覧
 */
export const Workers = () => {
  const [search, setSearch] = useState({
    page: 1,
    per_page: 100,
    orderby: "display_name",
    order: "asc",
    search: "",
  });
  const [selectedId, setSelectedId] = useState();
  const { data, error, isLoading } = useWorkers(search);
  const changeSortByName = () => {
    setSearch({
      ...search,
      orderby: "display_name",
      order: search.order === "asc" ? "desc" : "asc",
    });
  };

  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <BaseLicensePage>
      <Header title={__("Workers", "yoyaku-manager")} />
      <SearchForm
        searchText={search.search}
        setSearchText={(value) =>
          setSearch({ ...search, search: value, page: 1 })
        }
      />

      <Table striped hover>
        <thead>
          <tr>
            <th width="40%" onClick={changeSortByName}>
              {__("Display Name", "yoyaku-manager")}
              <SortIcon order={search.order} />
            </th>
            <th>{__("Email", "yoyaku-manager")}</th>
            <th>{__("Role", "yoyaku-manager")}</th>
          </tr>
        </thead>
        <tbody>
          {data.items.map((item) => (
            <tr
              key={item.id}
              onMouseEnter={() => setSelectedId(item.id)}
              onMouseLeave={() => setSelectedId(null)}
            >
              <td>{`${item.display_name}`}</td>
              <td>{item.user_email}</td>
              <td>{item.role}</td>
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
