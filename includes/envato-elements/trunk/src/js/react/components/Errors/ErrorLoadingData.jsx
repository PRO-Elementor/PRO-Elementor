import React from 'react'
import PropTypes from 'prop-types'
import styles from './ErrorLoadingData.module.scss'

const ErrorLoadingData = ({ message }) => (
  <div className={styles.message}><p className={styles.copy}>{message}</p></div>
)

ErrorLoadingData.propTypes = {
  message: PropTypes.string
}

ErrorLoadingData.defaultProps = {
  message: 'Sorry there was an error loading this data. Please try again.'
}
export default ErrorLoadingData
