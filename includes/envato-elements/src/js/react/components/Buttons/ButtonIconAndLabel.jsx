import React from 'react'
import PropTypes from 'prop-types'
import styles from './ButtonIconAndLabel.module.scss'

const ButtonIconAndLabel = ({ label, icon }) => {
  const iconClasses = ['dashicons', iconToDashIconMappings[icon], styles.icon]
  if (label) {
    iconClasses.push(styles.iconWithLabel)
  }

  return (
    <>
      {icon ? <span className={iconClasses.join(' ')} /> : null}
      {label}
    </>
  )
}

const iconToDashIconMappings = {
  arrow: 'dashicons-arrow-right-alt2',
  tick: 'dashicons-yes',
  info: 'dashicons-info',
  eye: 'dashicons-visibility',
  cross: 'dashicons-dismiss',
  update: 'dashicons-update',
  updateSpinning: `dashicons-update ${styles.iconSpinning}`,
  link: 'dashicons-external',
  plus: 'dashicons-plus-alt',
  trash: 'dashicons-trash',
  download: 'dashicons-download',
  expand: 'dashicons-editor-expand'
}

ButtonIconAndLabel.propTypes = {
  label: PropTypes.string,
  icon: PropTypes.oneOf(Object.keys(iconToDashIconMappings))
}

ButtonIconAndLabel.defaultProps = {
  label: null,
  icon: null
}

export default ButtonIconAndLabel
