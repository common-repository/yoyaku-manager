import apiFetch from "@wordpress/api-fetch";

/**
 * https://developer.wordpress.org/block-editor/reference-guides/packages/packages-api-fetch/
 */
export const fetcher = (path) =>
  apiFetch({
    path: path,
    method: "GET",
  })
    .then((response) => {
      console.log(response);
      return response;
    })
    .catch((error) => {
      // If the browser doesn't support AbortController then the code below will never log.
      // However, in most cases this should be fine as it can be considered to be a progressive enhancement.
      if (error.name === "AbortError") {
        console.log("Request has been aborted");
      }
      throw error;
    });
