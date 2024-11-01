import {Pagination as BSPagination} from "react-bootstrap";

/**
 * @param currentPage
 * @param numPages
 * @param setPage
 */
export const Pagination = ({ currentPage = 1, numPages, setPage }) => {
  const hasPrev = 0 < currentPage - 1;
  const hasNext = currentPage + 1 <= numPages;

  if (numPages <= 1) {
    return;
  }

  return (
    <div>
      <BSPagination className="justify-content-center">
        <BSPagination.First disabled={!hasPrev} onClick={() => setPage(1)} />

        <BSPagination.Prev
          disabled={!hasPrev}
          onClick={() => setPage(currentPage - 1)}
        />

        {hasPrev && (
          <BSPagination.Item onClick={() => setPage(currentPage - 1)}>
            {currentPage - 1}
          </BSPagination.Item>
        )}

        <BSPagination.Item active>{currentPage}</BSPagination.Item>

        {hasNext && (
          <BSPagination.Item onClick={() => setPage(currentPage + 1)}>
            {currentPage + 1}
          </BSPagination.Item>
        )}

        <BSPagination.Next
          disabled={!hasNext}
          onClick={() => setPage(currentPage + 1)}
        />

        <BSPagination.Last
          disabled={!hasNext}
          onClick={() => setPage(numPages)}
        />
      </BSPagination>
    </div>
  );
};
