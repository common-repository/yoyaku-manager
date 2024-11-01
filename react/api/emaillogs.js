import { YOYAKU_ROUTE_NAMESPACE } from "@/utils/consts";
import apiFetch from "@wordpress/api-fetch";
import useSWR from "swr";
import { getQueryString } from "./index";

const REST_BASE = YOYAKU_ROUTE_NAMESPACE + "/emaillogs";

export const useEmailLogs = (searchParams) =>
  useSWR(REST_BASE + getQueryString(searchParams), {
    fallbackData: { items: [], all_count: 0, failed_count: 0 },
  });

export const useEmailLog = (id) => useSWR(`${REST_BASE}/${id}`);

export const deleteEmailLogAPI = async (id) => {
  return await apiFetch({
    path: `${REST_BASE}/${id}`,
    method: "DELETE",
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const sendUndeliveredEmailAPI = async (data = {}) => {
  return await apiFetch({
    path: `${REST_BASE}/send-undelivered`,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};
