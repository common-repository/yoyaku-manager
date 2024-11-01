import { YOYAKU_ROUTE_NAMESPACE } from "@/utils/consts";
import apiFetch from "@wordpress/api-fetch";
import useSWR from "swr";
import { getQueryString } from "./index";

const REST_BASE = YOYAKU_ROUTE_NAMESPACE + "/event-bookings";
const REST_BASE_FRONT = YOYAKU_ROUTE_NAMESPACE + "/front/event-bookings";

export const useEventBookings = (searchParams) =>
  useSWR(REST_BASE + getQueryString(searchParams), {
    fallbackData: { items: [], num_pages: 0, total: 0 },
  });

export const useEventBooking = (id) => useSWR(`${REST_BASE}/${id}`);

export const addEventBookingAPI = async (data) => {
  return await apiFetch({
    path: `${REST_BASE}`,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const updateEventBookingAPI = async (data) => {
  return await apiFetch({
    path: `${REST_BASE}/${data.id}`,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const updateEventBookingStatusAPI = async (data) => {
  return await apiFetch({
    path: `${REST_BASE}/status/${data.id}`,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const deleteEventBookingAPI = async (id) => {
  return await apiFetch({
    path: `${REST_BASE}/${id}`,
    method: "DELETE",
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const frontAddEventBooking = async (data) => {
  return await apiFetch({
    path: `${REST_BASE_FRONT}`,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const frontCancelEventBooking = async (data) => {
  return await apiFetch({
    path: `${REST_BASE_FRONT}/cancel`,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const exportBookingsAPI = async (searchParams) => {
  const path = `${REST_BASE}/export` + getQueryString(searchParams);
  return await apiFetch({
    path: path,
    method: "GET",
  }).catch((error) => {
    console.error(error);
  });
};
