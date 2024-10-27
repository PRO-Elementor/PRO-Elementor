import React, { useState } from 'react'
import TokenProjectSignup from '../Modal/TokenProjectSignup'
import ExternalLinkButton from './ExternalLinkButton'
import { tokenUrl } from '../../utils/linkGenerator'

const ActivateButton = () => {
  const [isActivationModelOpen, setOpenActivationModal] = useState(false)

  return (
    <>
      {isActivationModelOpen
        ? (
          <TokenProjectSignup onCloseCallback={() => {
            setOpenActivationModal(false)
          }}
          />
          )
        : null}
      <ExternalLinkButton
        type='primary'
        label='Get Started'
        icon='arrow'
        href={tokenUrl({ utm_content: 'get_started_button' })}
        openNewWindow
        onClick={() => {
          setOpenActivationModal(true)
        }}
      />
    </>
  )
}

export default ActivateButton
