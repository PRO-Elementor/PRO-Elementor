import React from 'react'
import Pagination from '../Search/Pagination'
import GridWrapper from '../Grid/GridWrapper'
import GridItem from '../Grid/GridItem'
import SearchResultItem from './SearchResultItem'
import MainHeading from '../Titles/MainHeading'
import NoResultsFound from '../Search/NoResultsFound'

const SearchResults = ({ searchResults, searchParams, onSearchSubmitted }) => {
  const numberOfResults = searchResults.results.search_query_result.search_payload.total_hits
  if (numberOfResults === 0) {
    return <NoResultsFound />
  }

  // Just checking if we have a search string to show the appropriate sub title string
  // if there is no search 1 will be the length as it returns page numbers as a key
  const isSearching = Object.keys(searchParams).length > 1
  const subTitleString = isSearching ? `Search results: ${numberOfResults}` : `Browse our collection of ${numberOfResults}`

  return (
    <>
      <MainHeading title='Premium Template Kits' subtitle={`${subTitleString} Premium Template Kit${numberOfResults > 1 ? 's' : ''}`} />
      <GridWrapper includeLastItemSpacer>
        {
          searchResults.results.search_query_result.search_payload.items.map(item => (
            <GridItem colWidthPercentage={33} key={item.humane_id}>
              <SearchResultItem item={item} />
            </GridItem>
          ))
        }
      </GridWrapper>

      <Pagination
        currentPage={searchResults.results.current_page}
        totalHits={numberOfResults}
        perPage={searchResults.results.per_page}
        searchParams={searchParams}
        onSearchSubmitted={onSearchSubmitted}
      />
    </>
  )
}

export default SearchResults
