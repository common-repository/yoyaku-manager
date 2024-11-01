/**
 * CustomersやEventsの名前ソートで使う関数
 * @param search
 */
export const changeSortByName = (search) => {
  let result = { ...search, orderby: "name" };
  if (result.order === "desc") {
    delete result.orderby;
    delete result.order;
  } else if (result.order === "asc") {
    result.order = "desc";
  } else {
    result.order = "asc";
  }
  return result;
};
