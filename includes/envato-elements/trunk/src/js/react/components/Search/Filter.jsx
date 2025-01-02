import React from 'react'
import styles from './Filter.module.scss'

/**
 * Filter drop down component (Orientation, Background, Colors).
 *
 * @param label
 * @param name
 * @param attributes
 * @param searchFilterChange
 * @param value
 * @param columns
 * @param variant
 * @returns {*}
 * @constructor
 */
const Filter = ({ label, name, attributes, searchFilterChange, value, columns = 3, variant = 'text', updateTitleWithCurrent = true }) => {
  // We decode the value to handle cases where %26 needs to be decoded to & in order for a string match to occur below:
  value = decodeURIComponent(value)
  // If a filter is selected we use the value as the main label on display.
  let filterLabel = label
  if (updateTitleWithCurrent) {
    // If we choose to update the main drop down label with the currently selected value this loop finds it
    attributes.filter((attribute) => {
      if (attribute.key && value === attribute.key) {
        filterLabel = attribute.value || attribute.key
      }
      return false
    })
  }
  return (
    <div className={styles.filter}>
      <div className={styles.filterLabel}>
        {filterLabel}
        <div className={styles.filterAttributes} data-columns={columns} data-variant={variant}>
          <div className={styles.filterAttributesContent}>
            {attributes.map((attribute) =>
              attribute.key && attribute.key.length > 0
                ? (
                  <div key={attribute.key} className={`${styles.filterAttribute} ${value === attribute.key ? styles.filterAttributeActive : ''}`}>
                    <label htmlFor={`filter${name}${attribute.key}`}>
                      <input
                        type='checkbox'
                        className={`${styles.filterAttributeCheckbox} ${
                        variant === 'colors' ? styles[`filterAttributeCheckbox--${attribute.key.toLowerCase()}`] : ''
                      }`}
                        name={attribute.key}
                        checked={value === attribute.key}
                        data-testid={`filter${name}${attribute.key}`}
                        id={`filter${name}${attribute.key}`}
                        onChange={(e) => {
                          const newsearchArguments = {}
                          newsearchArguments[name] = value === attribute.key ? '' : attribute.key
                          newsearchArguments.page = 1
                          searchFilterChange(newsearchArguments, true)
                        }}
                      />
                      {attribute.value || attribute.key}
                    </label>
                  </div>
                  )
                : null
            )}
          </div>
        </div>
      </div>
    </div>
  )
}

export default Filter
