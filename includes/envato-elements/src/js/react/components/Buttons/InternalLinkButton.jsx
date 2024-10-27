import React from 'react'
import PropTypes from 'prop-types'
import ButtonIconAndLabel from './ButtonIconAndLabel'
import ButtonElement from './ButtonElement'

const InternalLinkButton = ({ type, label, icon, href }) => {
  return (
    <ButtonElement element='Link' to={href} type={type}>
      <ButtonIconAndLabel label={label} icon={icon} />
    </ButtonElement>
  )
}

InternalLinkButton.propTypes = {
  type: PropTypes.string,
  label: PropTypes.string,
  icon: PropTypes.string,
  href: PropTypes.string.isRequired
}

InternalLinkButton.defaultProps = {
  type: 'ghost',
  label: null,
  icon: null
}

export default InternalLinkButton
