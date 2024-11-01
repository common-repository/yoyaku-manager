import { YOYAKU_ROUTE_NAMESPACE } from "@/utils/consts";
import apiFetch from "@wordpress/api-fetch";
import useSWR from "swr";
import { getQueryString } from "./index";

const REST_BASE = YOYAKU_ROUTE_NAMESPACE + "/event-periods";

export const useEventPeriods = (searchParams) =>
  useSWR(REST_BASE + getQueryString(searchParams), {
    fallbackData: { items: [], num_pages: 0, total: 0 },
  });

/**
 *
 * @param id {int|string} id or uuid
 * @returns {object}
 */
export const useEventPeriod = (id) => useSWR(`${REST_BASE}/${id}`);

export const addEventPeriodAPI = async (data) => {
  return await apiFetch({
    path: `${REST_BASE}`,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const updateEventPeriodAPI = async (data) => {
  return await apiFetch({
    path: `${REST_BASE}/${data.id}`,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const deleteEventPeriodAPI = async (id) => {
  return await apiFetch({
    path: `${REST_BASE}/${id}`,
    method: "DELETE",
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const deleteZoomMeetingAPI = async (id) => {
  return await apiFetch({
    path: `${REST_BASE}/${id}/delete-meeting`,
    method: "DELETE",
    data: { service: "zoom" },
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const deleteGoogleMeetAPI = async (id) => {
  return await apiFetch({
    path: `${REST_BASE}/${id}/delete-meeting`,
    method: "DELETE",
    data: { service: "google" },
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};
