import React, { useState } from 'react'
import SearchUi from './SearchUi'
import SearchResults from './SearchResults'
import LoadingAnimation from '../Loading/LoadingAnimation'
import ErrorLoadingData from '../Errors/ErrorLoadingData'
import fetchSearchResultsFromAPI from '../../api/fetchPhotosSearchResults'

const SearchWrapper = ({ searchParams, onSearchSubmitted }) => {
  const { loading, data, error } = fetchSearchResultsFromAPI(searchParams)
  const [layout, setLayout] = useState('masonry') // Default to a masonry layout

  const aggregations = !loading && !error && data && data.results ? data.results.search_query_result.search_payload.aggregations : {}

  return (
    <>
      <SearchUi
        searchParams={searchParams}
        onSearchSubmitted={onSearchSubmitted}
        aggregations={aggregations}
        layout={layout}
        setLayout={setLayout}
      />

      {loading ? <LoadingAnimation /> : null}
      {error ? <ErrorLoadingData /> : null}
      {!loading && !error && data
        ? (
          <SearchResults
            searchResults={data}
            searchParams={searchParams}
            onSearchSubmitted={onSearchSubmitted}
            layout={layout}
          />
          )
        : null}
    </>
  )
}

export default SearchWrapper
