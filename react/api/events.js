import { YOYAKU_ROUTE_NAMESPACE } from "@/utils/consts";
import apiFetch from "@wordpress/api-fetch";
import useSWR from "swr";
import { getQueryString } from "./index";

const REST_BASE = YOYAKU_ROUTE_NAMESPACE + "/events";

export const useEvents = (searchParams) =>
  useSWR(REST_BASE + getQueryString(searchParams), {
    fallbackData: { items: [], num_pages: 0, total: 0 },
  });

export const useCalendarEvents = (searchParams) =>
  useSWR(REST_BASE + "/calendar" + getQueryString(searchParams), {
    fallbackData: [],
  });

export const useEvent = (id) => useSWR(id ? `${REST_BASE}/${id}` : null);

export const addEventAPI = async (data) => {
  return await apiFetch({
    path: REST_BASE,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const updateEventAPI = async (data) => {
  return await apiFetch({
    path: `${REST_BASE}/${data.id}`,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const deleteEventAPI = async (id) => {
  return await apiFetch({
    path: `${REST_BASE}/${id}`,
    method: "DELETE",
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};
