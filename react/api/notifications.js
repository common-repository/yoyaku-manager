import { YOYAKU_ROUTE_NAMESPACE } from "@/utils/consts";
import apiFetch from "@wordpress/api-fetch";
import useSWR from "swr";
import { getQueryString } from "./index";

const REST_BASE = YOYAKU_ROUTE_NAMESPACE + "/notifications";

export const useNotifications = (searchParams) =>
  useSWR(REST_BASE + getQueryString(searchParams), {
    fallbackData: { items: [] },
  });

export const useNotification = (id) => useSWR(id ? `${REST_BASE}/${id}` : null);

export const addNotificationAPI = async (data) => {
  return await apiFetch({
    path: `${REST_BASE}/`,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const updateNotificationAPI = async (data) => {
  return await apiFetch({
    path: `${REST_BASE}/${data.id}`,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const deleteNotificationAPI = async (id) => {
  return await apiFetch({
    path: `${REST_BASE}/${id}`,
    method: "DELETE",
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};
