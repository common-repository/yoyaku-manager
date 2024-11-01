export * from "./customers";
export * from "./event-bookings";
export * from "./event-periods";
export * from "./event-tickets";
export * from "./events";
export * from "./emaillogs";
export * from "./notifications";
export * from "./settings";
export * from "./workers";

/**
 * クエリ文字列を取得する
 * @param {object} params クエリのパラメーター {key: value}
 * @returns {string}
 */
export const getQueryString = (params) => {
  // null や undefined のキーを削除する
  Object.keys(params).forEach(
    (key) =>
      (params[key] === undefined || params[key] === null) && delete params[key],
  );

  const searchParams = new URLSearchParams(params);
  return searchParams.toString() ? "?" + searchParams.toString() : "";
};
