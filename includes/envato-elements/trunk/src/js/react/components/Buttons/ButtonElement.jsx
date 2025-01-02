import React from 'react'
import PropTypes from 'prop-types'
import { Link } from 'react-router-dom'
import styles from './ButtonElement.module.scss'

const ButtonElement = ({ type, dataTestId, element, children, ...additionalProps }) => {
  if (element === 'button') {
    return (
      <button className={styles[type]} data-testid={dataTestId} {...additionalProps}>
        {children}
      </button>
    )
  }

  if (element === 'Link') {
    return (
      <Link className={styles[type]} data-testid={dataTestId} {...additionalProps}>
        {children}
      </Link>
    )
  }

  if (element === 'a') {
    return (
      <a className={styles[type]} data-testid={dataTestId} {...additionalProps}>
        {children}
      </a>
    )
  }

  if (element === 'label') {
    return (
      <label className={styles[type]} data-testid={dataTestId} {...additionalProps}>
        {children}
      </label>
    )
  }
}

ButtonElement.propTypes = {
  type: PropTypes.oneOf(['ghost', 'primary', 'secondary', 'warning', 'attention']),
  dataTestId: PropTypes.string,
  element: PropTypes.oneOf(['button', 'a', 'Link', 'label']).isRequired,
  children: PropTypes.node
}

ButtonElement.defaultProps = {
  type: 'ghost',
  dataTestId: null,
  children: null
}

export default ButtonElement
