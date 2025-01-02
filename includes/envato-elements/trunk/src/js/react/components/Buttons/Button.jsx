import React from 'react'
import PropTypes from 'prop-types'
import ButtonIconAndLabel from './ButtonIconAndLabel'
import ButtonElement from './ButtonElement'

const Button = ({ type, label, icon, onClick, disabled, dataTestId }) => {
  return (
    <ButtonElement element='button' type={type} onClick={onClick} disabled={disabled} dataTestId={dataTestId}>
      <ButtonIconAndLabel label={label} icon={icon} />
    </ButtonElement>
  )
}

Button.propTypes = {
  type: PropTypes.string,
  label: PropTypes.string,
  icon: PropTypes.string,
  onClick: PropTypes.func,
  disabled: PropTypes.bool,
  dataTestId: PropTypes.string
}

Button.defaultProps = {
  type: 'ghost',
  label: null,
  icon: null,
  onClick: null,
  disabled: false,
  dataTestId: null
}

export default Button
