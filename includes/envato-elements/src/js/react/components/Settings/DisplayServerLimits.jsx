import React from 'react'
import getServerLimits from '../../api/getServerLimits'
import LoadingAnimation from '../Loading/LoadingAnimation'

import styles from './DisplayServerLimits.module.scss'

const DisplayServerLimits = () => {
  const { loading, data, error } = getServerLimits()

  if (loading) {
    return (
      <LoadingAnimation />
    )
  }

  if (error) {
    return (
      <p className={styles.copy}><strong>Error:</strong> Failed to check server limits. Please contact hosting provider and ask them to investigate any errors in the logs and to raise the PHP memory limits for you.</p>
    )
  }

  if (data) {
    return (
      <ul>
        {Object.keys(data.limits).map(limit => (
          <li key={limit} data-limit={limit} className={data.limits[limit].ok ? styles.limitOk : styles.limitError}>
            <span className={styles.limitTitle}>
              {data.limits[limit].title}
            </span>
            <span className={styles.limitMessage}>
              {data.limits[limit].message}
            </span>
          </li>
        ))}
      </ul>
    )
  }

  return null
}

export default DisplayServerLimits
