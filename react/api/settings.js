import { YOYAKU_ROUTE_NAMESPACE } from "@/utils/consts";
import apiFetch from "@wordpress/api-fetch";
import useSWR from "swr";

const REST_BASE = YOYAKU_ROUTE_NAMESPACE + "/settings";

export const useSettings = () => useSWR(REST_BASE);

export const updateSettings = async (data) => {
  return await apiFetch({
    path: REST_BASE,
    method: "POST",
    data: data,
  }).catch((error) => {
    error.is_error = true;
    return error;
  });
};
