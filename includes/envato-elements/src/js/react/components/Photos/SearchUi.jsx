import React from 'react'
import TextInput from '../Search/TextInput'
import Filter from '../Search/Filter'
import styles from './SearchUi.module.scss'

const SearchUi = ({ searchParams, onSearchSubmitted, aggregations, layout, setLayout }) => {
  return (
    <div className={styles.wrapper}>
      <TextInput
        searchParams={searchParams} onSearchSubmitted={(args) => {
          onSearchSubmitted({ ...args })
        }}
      />
      {Object.keys(aggregations).length > 0
        ? (
          <>
            <div className={styles.searchFilter}>
              <button
                type='button'
                className={`${styles.viewToggle} ${
                  layout === 'masonry' ? styles.viewToggleMasonry : styles.viewToggleGrid
              }`}
                onClick={(e) => {
                  e.preventDefault()
                  setLayout(layout === 'masonry' ? 'square' : 'masonry')
                  return false
                }}
              >
                View
                <svg
                  xmlns='http://www.w3.org/2000/svg'
                  width='20'
                  height='20'
                  fill='none'
                  viewBox='0 0 20 20'
                  className={styles.viewToggleGridIcon}
                >
                  <path
                    fill='#888'
                    fillRule='evenodd'
                    d='M2 1h16c.55 0 1 .45 1 1v16c0 .55-.45 1-1 1H2c-.55 0-1-.45-1-1V2c0-.55.45-1 1-1zm7.01 7.99v-6H3v6h6.01zm8 0v-6h-6v6h6zm-8 8.01v-6H3v6h6.01zm8 0v-6h-6v6h6z'
                    clipRule='evenodd'
                  />
                </svg>
                <svg
                  xmlns='http://www.w3.org/2000/svg'
                  width='20'
                  height='20'
                  fill='none'
                  viewBox='0 0 20 20'
                  className={styles.viewToggleMasonryIcon}
                >
                  <path
                    fill='#888'
                    fillRule='evenodd'
                    d='M1 18V2c0-.55.45-1 1-1h16c.55 0 1 .45 1 1v16c0 .55-.45 1-1 1H2c-.55 0-1-.45-1-1zm10-7H3v6h8v-6zM6 3H3v6h3V3zm11 8h-4v6h4v-6zm0-8H8v6h9V3z'
                    clipRule='evenodd'
                  />
                  <mask id='a' width='18' height='18' x='1' y='1' maskUnits='userSpaceOnUse'>
                    <path
                      fill='#fff'
                      fillRule='evenodd'
                      d='M1 18V2c0-.55.45-1 1-1h16c.55 0 1 .45 1 1v16c0 .55-.45 1-1 1H2c-.55 0-1-.45-1-1zm10-7H3v6h8v-6zM6 3H3v6h3V3zm11 8h-4v6h4v-6zm0-8H8v6h9V3z'
                      clipRule='evenodd'
                    />
                  </mask>
                  <g mask='url(#a)'>
                    <path
                      fill='#0878B0'
                      d='M-0.241 20.816H21.605V41.581H-0.241z'
                      transform='rotate(-90 -.241 20.816)'
                    />
                  </g>
                </svg>
              </button>
              {aggregations.orientation
                ? (
                  <Filter
                    searchFilterChange={(args) => {
                      onSearchSubmitted({ ...searchParams, ...args })
                    }}
                    label='Orientation'
                    name='orientation'
                    variant='checkbox'
                    value={searchParams.orientation}
                    attributes={aggregations.orientation.buckets}
                    columns={1}
                  />
                  )
                : null}
              {aggregations.background
                ? (
                  <Filter
                    searchFilterChange={(args) => {
                      onSearchSubmitted({ ...searchParams, ...args })
                    }}
                    label='Background'
                    name='background'
                    variant='checkbox'
                    value={searchParams.background}
                    attributes={aggregations.background.buckets}
                    columns={1}
                  />
                  )
                : null}
              {aggregations.colors
                ? (
                  <Filter
                    searchFilterChange={(args) => {
                      onSearchSubmitted({ ...searchParams, ...args })
                    }}
                    label='Colors'
                    columns={2}
                    variant='colors'
                    name='colors'
                    value={searchParams.colors}
                    attributes={aggregations.colors.buckets}
                  />
                  )
                : null}
            </div>
          </>
          )
        : null}
    </div>
  )
}

export default SearchUi
