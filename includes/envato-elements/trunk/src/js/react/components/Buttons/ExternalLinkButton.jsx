import React from 'react'
import PropTypes from 'prop-types'
import ButtonElement from './ButtonElement'
import ButtonIconAndLabel from './ButtonIconAndLabel'

const ExternalLinkButton = ({ type, label, icon, href, openNewWindow, ...additionalProps }) => {
  const openNewWindowProps = openNewWindow ? { target: '_blank', rel: 'noopener noreferrer' } : null

  return (
    <ButtonElement href={href} type={type} element='a' {...openNewWindowProps} {...additionalProps}>
      <ButtonIconAndLabel label={label} icon={icon} />
    </ButtonElement>
  )
}

ExternalLinkButton.propTypes = {
  type: PropTypes.string,
  label: PropTypes.string,
  icon: PropTypes.string,
  href: PropTypes.string.isRequired
}

ExternalLinkButton.defaultProps = {
  type: 'ghost',
  label: null,
  icon: null
}

export default ExternalLinkButton
