import React, { useEffect, useState } from 'react'
import styles from './TextInput.module.scss'

/**
 *
 *
 * @param defaultText
 * @param placeholderText
 * @param onSearchSubmitted
 * @returns {*}
 * @constructor
 */
const TextInput = ({ searchParams, placeholderText = 'Search...', onSearchSubmitted }) => {
  const [searchText, setSearchText] = useState(searchParams.text || '')
  useEffect(() => {
    setSearchText(searchParams.text || '')
  }, [searchParams])
  return (
    <div className={styles.searchText}>
      <form onSubmit={e => {
        e.preventDefault()
        onSearchSubmitted({ text: searchText })
        return false
      }}
      >
        <input
          type='text'
          value={searchText}
          placeholder={placeholderText}
          data-cy='elements-search-text'
          onChange={e => { setSearchText(e.target.value) }}
          className={styles.searchTextInput}
          style={{ width: '100%' }}
        />
        <button
          type='submit'
          name='go'
          className={`dashicons dashicons-search ${styles.searchTextSubmit}`}
          data-cy='elements-search-submit-licensed'
        />
      </form>
    </div>
  )
}

export default TextInput
