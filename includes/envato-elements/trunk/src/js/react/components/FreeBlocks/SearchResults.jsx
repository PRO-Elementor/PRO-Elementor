import React from 'react'
import GridWrapper from '../Grid/GridWrapper'
import GridItem from '../Grid/GridItem'
import SearchResultItem from './SearchResultItem'

const SearchResults = ({ searchResults, searchParams, onSearchSubmitted, aggregations }) => {
  return (
    <>
      <GridWrapper includeLastItemSpacer>
        {
          searchResults.items.map(item => (
            <GridItem colWidthPercentage={33} key={item.id}>
              <SearchResultItem item={item} />
            </GridItem>
          ))
        }
      </GridWrapper>
    </>
  )
}

export default SearchResults
