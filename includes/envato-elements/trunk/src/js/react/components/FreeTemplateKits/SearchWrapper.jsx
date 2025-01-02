import React from 'react'
import SearchUi from './SearchUi'
import SearchResults from './SearchResults'
import LoadingAnimation from '../Loading/LoadingAnimation'
import ErrorLoadingData from '../Errors/ErrorLoadingData'
import fetchSearchResultsFromAPI from '../../api/fetchFreeTemplateKitSearchResults'

const SearchWrapper = ({ searchParams, onSearchSubmitted }) => {
  const { loading, data, error } = fetchSearchResultsFromAPI(searchParams)

  const aggregations = !loading && !error && data && data.meta ? data.meta : {}

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
