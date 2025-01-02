import React from 'react'
import TextInput from '../Search/TextInput'
import Filter from '../Search/Filter'
import styles from './SearchUi.module.scss'

const FreeTemplateKits = ({ searchParams, onSearchSubmitted, aggregations }) => {
  return (
    <div className={styles.wrapper}>
      <TextInput
        searchParams={searchParams} onSearchSubmitted={(args) => {
          onSearchSubmitted({ ...args })
        }}
      />
      {aggregations
        ? (
          <div className={styles.searchFilter}>
            {aggregations.industries
              ? (
                <Filter
                  searchFilterChange={(args) => {
                    onSearchSubmitted({ ...searchParams, ...args })
                  }}
                  label='Categories'
                  name='industry'
                  value={searchParams.industries}
                  attributes={aggregations.industries}
                />
                )
              : null}
          </div>
          )
        : null}
    </div>
  )
}

export default FreeTemplateKits
