import React from 'react'
import SearchResults from './SearchResults'
import LoadingAnimation from '../Loading/LoadingAnimation'
import ErrorLoadingData from '../Errors/ErrorLoadingData'
import fetchFreeBlockSearchResults from '../../api/fetchFreeBlockSearchResults'
import GridWrapper from '../Grid/GridWrapper'
import GridItem from '../Grid/GridItem'
import { Link, useRouteMatch } from 'react-router-dom'
import styles from './SearchWrapper.module.scss'
import MainHeading from '../Titles/MainHeading'

const SearchWrapper = ({ searchParams, onSearchSubmitted }) => {
  const { url } = useRouteMatch()
  const { loading, data, error } = fetchFreeBlockSearchResults(searchParams)
  const aggregations = !loading && !error && data && data.meta ? data.meta : {}

  if (loading) return <LoadingAnimation />
  if (error) return <ErrorLoadingData />
  if (!data || !data.meta) return <ErrorLoadingData />

  const currentCategoryName = searchParams.category
    ? aggregations.categories.reduce((i, k) => {
      return k.key === searchParams.category ? k.value : i
    }, '')
    : null

  return (
    <>
      <div className={styles.headerWrapper}>
        <MainHeading title={`Free Blocks${currentCategoryName ? `: ${currentCategoryName}` : ''}`} />
        <div className={styles.currentBlockCategory}>
          <div className={styles.optionBlockCategoryWrapper}>
            <div className={styles.optionBlockCategory}>
              <Link
                to={url}
                className={styles.optionBlockCategoryLink}
              >All Categories
              </Link>
            </div>
            {aggregations.categories.map(category => (
              <div className={styles.optionBlockCategory} key={category.key}>
                <Link
                  to={`${url}/category-${category.key}/`}
                  className={`${styles.optionBlockCategoryLink} ${searchParams.category === category.key ? styles.optionBlockCategoryLinkCurrent : ''}`}
                >
                  {category.value}
                </Link>
              </div>
            ))}
          </div>
        </div>
      </div>
      {!searchParams.category
        ? (
          <GridWrapper>
            {aggregations.categories.map(category => (
              <GridItem className={styles.blockCategoryName} key={category.key}>
                <Link className={styles.blockCategoryNameLink} to={`${url}/category-${category.key}/`}>
                  {category.value}
                </Link>
              </GridItem>
            ))}
          </GridWrapper>
          )
        : (
          <SearchResults
            searchResults={data}
            searchParams={searchParams}
            onSearchSubmitted={onSearchSubmitted}
            aggregations={aggregations}
          />
          )}
    </>
  )
}

export default SearchWrapper
