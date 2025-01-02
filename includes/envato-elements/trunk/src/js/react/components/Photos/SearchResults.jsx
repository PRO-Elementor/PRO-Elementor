import React from 'react'
import PhotoItemCard from './PhotoItemCard'
import Pagination from '../Search/Pagination'
import GridWrapper from '../Grid/GridWrapper'
import GridItem from '../Grid/GridItem'
import splitItemsIntoRows from '../../utils/splitItemsIntoRows'
import NoResultsFound from '../Search/NoResultsFound'
import styles from './SearchResults.module.scss'

const SearchResults = ({ searchResults, searchParams, onSearchSubmitted, layout }) => {
  if (searchResults.results.search_query_result.search_payload.total_hits === 0) {
    return <NoResultsFound />
  }
  const getBreakpointsImages = (items) => {
    const breakpoints = [
      {
        breakpoint: 'large',
        itemsPerRow: 5,
        gutterWidth: 1.1
      }
    ]
    const numberOfRows = Number.MAX_SAFE_INTEGER
    let sliceIndex = 0

    const breakpointsImages = breakpoints.map((breakpoint, i) => {
      const itemRows = splitItemsIntoRows(items, breakpoint.itemsPerRow, numberOfRows)

      const imagesConfig = itemRows.map((itemRow, rowIndex) =>
        itemRow.items.map((item, imageIndex) => {
          const numOfGutters = itemRow.items.length - 1
          const smallLastRow = rowIndex === itemRows.length - 1 && itemRow.size < breakpoint.itemsPerRow * 0.75 // scale up if close enough
          const rowSize = smallLastRow ? breakpoint.itemsPerRow : itemRow.size

          const { aspectRatio = 1 } = item
          const calculatedMasonryWidth = (aspectRatio / rowSize) * (100 - numOfGutters * breakpoint.gutterWidth)

          return {
            ...item,
            calculatedMasonryWidth
          }
        })
      )

      if (sliceIndex < imagesConfig.length) sliceIndex = imagesConfig.length

      return {
        breakpoint: {
          ...breakpoint,
          // size: idx(gridBreakpoints, (_) => _[breakpoint.breakpoint]) || 0,
          size: breakpoint.breakpoint || 0
        },
        images: imagesConfig.flat()
      }
    })

    return { breakpointsImages, sliceIndex }
  }

  const pagination = () => (
    <Pagination
      currentPage={searchResults.results.current_page}
      totalHits={searchResults.results.search_query_result.search_payload.total_hits}
      perPage={searchResults.results.per_page}
      searchParams={searchParams}
      onSearchSubmitted={onSearchSubmitted}
    />
  )

  if (layout === 'masonry') {
    const { breakpointsImages } = getBreakpointsImages(searchResults.results.search_query_result.search_payload.items)
    const items = breakpointsImages[0].images

    return (
      <>
        <div className={styles.masonryWrapper}>
          {items.map(item => (
            <PhotoItemCard key={item.humane_id} layout={layout} item={item} />
          ))}
        </div>

        {pagination()}
      </>
    )
  }

  return (
    <>
      <GridWrapper includeLastItemSpacer>
        {searchResults.results.search_query_result.search_payload.items.map(item => (
          <GridItem key={item.humane_id} colWidthPercentage={20}>
            <PhotoItemCard layout={layout} item={item} />
          </GridItem>
        ))}
      </GridWrapper>

      {pagination()}
    </>
  )
}

export default SearchResults
