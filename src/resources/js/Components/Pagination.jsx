export default function Pagination({ paginator }) {
  const {
    first_page_url,
    last_page_url,
    next_page_url,
    prev_page_url,
    current_page,
    last_page
  } = paginator;

  return (
    <div className="pagination">
      <a
        href={first_page_url}
        className="pagination-link"
        aria-disabled={current_page === 1}
      >
        &laquo;
      </a>

      <a
        href={prev_page_url}
        className="pagination-link"
        aria-disabled={!prev_page_url}
      >
        &lt;
      </a>

      <span className="current-page">
        {current_page} / {last_page}
      </span>

      <a
        href={next_page_url}
        className="pagination-link"
        aria-disabled={!next_page_url}
      >
        &gt;
      </a>

      <a
        href={last_page_url}
        className="pagination-link"
        aria-disabled={current_page === last_page}
      >
        &raquo;
      </a>
    </div>
  );
}
