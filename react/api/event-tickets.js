import { YOYAKU_ROUTE_NAMESPACE } from "@/utils/consts";
import apiFetch from "@wordpress/api-fetch";
import useSWR from "swr";
import { getQueryString } from "./index";

const REST_BASE = YOYAKU_ROUTE_NAMESPACE + "/event-tickets";
const REST_BASE_FRONT = YOYAKU_ROUTE_NAMESPACE + "/front/event-tickets";

export const useTickets = (searchParams) =>
  useSWR(REST_BASE + getQueryString(searchParams), {
    fallbackData: { items: [] },
  });

/**
 *
 * @param data  {object|null}
 * @returns {object}
 */
export const useTicketsWithSoldCount = (data) => {
  return useSWR(REST_BASE + getQueryString(data));
};

export const useTicket = (id) => useSWR(id ? `${REST_BASE}/${id}` : null);

export const useTicketsWithSoldCountForFront = (uuid) =>
  useSWR(REST_BASE_FRONT + getQueryString({ event_period_uuid: uuid }), {
    fallbackData: { items: [] },
  });

export const addTicketAPI = async (data) => {
  return await apiFetch({
    path: `${REST_BASE}`,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const updateTicketAPI = async (data) => {
  return await apiFetch({
    path: `${REST_BASE}/${data.id}`,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const deleteTicketAPI = async (id) => {
  return await apiFetch({
    path: `${REST_BASE}/${id}`,
    method: "DELETE",
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};
