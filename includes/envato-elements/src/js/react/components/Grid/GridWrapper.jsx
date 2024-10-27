import React from 'react'
import styles from './GridWrapper.module.scss'

const GridWrapper = ({ children, includeLastItemSpacer = false }) => (
  <div className={styles.wrapper}>
    <div className={styles.inner}>
      {children}
      {includeLastItemSpacer ? <div className={styles.cardSpacing} /> : null}
    </div>
  </div>
)

export default GridWrapper
