import React from 'react'
import GridWrapper from '../Grid/GridWrapper'
import GridItem from '../Grid/GridItem'
import SearchResultItem from './SearchResultItem'
import MainHeading from '../Titles/MainHeading'
import Pagination from '../Search/Pagination'
import NoResultsFound from '../Search/NoResultsFound'

const SearchResults = ({ searchResults, searchParams, onSearchSubmitted }) => {
  const numberOfKits = searchResults.meta.total_items
  const numberOfTemplates = searchResults.meta.total_template_count
  if (numberOfKits === 0) {
    return <NoResultsFound />
  }

  // Just checking if we have a search string to show the appropriate sub title string
  // if there is no search 1 will be the length as it returns page numbers as a key
  const isSearching = Object.keys(searchParams).length > 1
  const subTitle = `${isSearching ? 'Search results:' : 'Browse our collection of'} 
        ${numberOfKits} Template Kit${numberOfKits > 1 ? 's' : ''} 
        including ${numberOfTemplates} Free Individual Templates`

  return (
    <>
      <MainHeading title='Free Template Kits' subtitle={subTitle} />
      <GridWrapper includeLastItemSpacer>
        {
          searchResults.items.map(item => (
            <GridItem colWidthPercentage={33} key={item.ID}>
              <SearchResultItem item={item} />
            </GridItem>
          ))
        }
      </GridWrapper>

      <Pagination
        currentPage={searchResults.meta.current_page}
        totalHits={numberOfKits}
        perPage={searchResults.meta.per_page}
        searchParams={searchParams}
        onSearchSubmitted={onSearchSubmitted}
      />
    </>
  )
}

export default SearchResults
