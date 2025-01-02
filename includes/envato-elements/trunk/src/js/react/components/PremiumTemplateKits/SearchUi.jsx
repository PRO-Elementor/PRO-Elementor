import React from 'react'
import TextInput from '../Search/TextInput'
import Filter from '../Search/Filter'
import styles from './SearchUi.module.scss'

// We have harded coded this list because there is variance in how elements handle
// special characters in the data.
const industriesForDropdown = [
  { key: 'Automotive & Transportation' },
  { key: 'Blogs & Podcasts' },
  { key: 'Business & Services' },
  { key: 'Creative & Design' },
  { key: 'Education' },
  { key: 'Events & Entertainment' },
  { key: 'Fashion & Beauty' },
  { key: 'Finance & Law' },
  { key: 'Food & Drink' },
  { key: 'Health & Medical' },
  { key: 'Kids & Babies' },
  { key: 'Miscellaneous' },
  { key: 'News & Magazines' },
  { key: 'Non-Profit & Religion' },
  { key: 'Personal & CV' },
  { key: 'Photography' },
  { key: 'Real Estate & Construction' },
  { key: 'Sport & Fitness' },
  { key: 'Technology & Apps' },
  { key: 'Travel & Accomodation' }
]

const PremiumTemplateKits = ({ searchParams, onSearchSubmitted, aggregations }) => {
  return (
    <div className={styles.wrapper}>
      <TextInput
        searchParams={searchParams} onSearchSubmitted={(args) => {
          onSearchSubmitted({ ...args })
        }}
      />
      {industriesForDropdown
        ? (
          <div className={styles.searchFilter}>
            <Filter
              searchFilterChange={(args) => {
                onSearchSubmitted({ ...searchParams, ...args })
              }}
              label='Categories'
              name='industries'
              value={searchParams.industries}
              attributes={industriesForDropdown}
              updateTitleWithCurrent={false}
            />
          </div>
          )
        : null}
    </div>
  )
}

export default PremiumTemplateKits
