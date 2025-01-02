import React from 'react'
import SearchUi from './SearchUi'
import SearchResults from './SearchResults'
import LoadingAnimation from '../Loading/LoadingAnimation'
import fetchSearchResultsFromAPI from '../../api/fetchPremiumTemplateKitSearchResults'
import ErrorLoadingData from '../Errors/ErrorLoadingData'

const SearchWrapper = ({ searchParams, onSearchSubmitted }) => {
  const { loading, data, error } = fetchSearchResultsFromAPI(searchParams)

  const aggregations = !error && !loading && data && data.results ? data.results.search_query_result.search_payload.aggregations : {}

  return (
    <>
      <SearchUi
        searchParams={searchParams}
        onSearchSubmitted={onSearchSubmitted}
        aggregations={aggregations}
      />

      {loading ? <LoadingAnimation /> : null}
      {error ? <ErrorLoadingData /> : null}
      {!loading && !error && data
        ? (
          <SearchResults
            searchResults={data}
            searchParams={searchParams}
            onSearchSubmitted={onSearchSubmitted}
          />
          )
        : null}
    </>
  )
}

export default SearchWrapper
