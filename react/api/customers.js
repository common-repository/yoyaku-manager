import { YOYAKU_ROUTE_NAMESPACE } from "@/utils/consts";
import apiFetch from "@wordpress/api-fetch";
import useSWR from "swr";
import { getQueryString } from "./index";

const REST_BASE = YOYAKU_ROUTE_NAMESPACE + "/customers";

export const useCustomers = (searchParams) =>
  useSWR(REST_BASE + getQueryString(searchParams), {
    fallbackData: { items: [], num_pages: 0, total: 0 },
  });

export const useCustomer = (id) => useSWR(`${REST_BASE}/${id}`);

export const addCustomerAPI = async (data) => {
  return await apiFetch({
    path: REST_BASE,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const updateCustomerAPI = async (data) => {
  return await apiFetch({
    path: `${REST_BASE}/${data.id}`,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const deleteCustomerAPI = async (id) => {
  return await apiFetch({
    path: `${REST_BASE}/${id}`,
    method: "DELETE",
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};

export const exportCustomersAPI = async () => {
  return await apiFetch({
    path: `${REST_BASE}/export`,
    method: "GET",
  }).catch((error) => {
    console.error(error);
  });
};
