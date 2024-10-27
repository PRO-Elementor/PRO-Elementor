import React from 'react'
import ReactPaginate from 'react-paginate'
import styles from './Pagination.module.scss'

/**

 currentPage: searchResults.results.current_page
 totalHits: searchResults.results.search_query_result.search_payload.total_hits
 perPage: searchResults.results.per_page

 */
const Pagination = ({ currentPage, totalHits, perPage, searchParams, onSearchSubmitted }) => {
  return (
    currentPage &&
    totalHits &&
    perPage &&
    totalHits > perPage
      ? (
        <ReactPaginate
          previousLabel={<span className={`dashicons dashicons-arrow-left-alt2 ${styles.previous}`} />}
          nextLabel={<span className={`dashicons dashicons-arrow-right-alt2 ${styles.next}`} />}
          breakLabel='...'
          breakClassName='break-me'
          pageCount={Math.min(40, Math.ceil(totalHits / perPage))}
          marginPagesDisplayed={2}
          pageRangeDisplayed={5}
          forcePage={parseInt(currentPage, 10) - 1}
          onPageChange={(data) => {
            window.scrollTo(0, 0)
            onSearchSubmitted({ ...searchParams, page: data.selected + 1 })
          }}
          containerClassName={styles.pagination}
          activeClassName={styles.paginationActive}
          disabledClassName={styles.paginationDisabled}
        />
        )
      : null
  )
}

export default Pagination
