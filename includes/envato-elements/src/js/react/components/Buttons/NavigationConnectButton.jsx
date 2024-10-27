import React, { useState } from 'react'
import TokenProjectSignup from '../Modal/TokenProjectSignup'
import useGlobalConfig from '../Contexts/useGlobalConfig'
import InternalLinkButton from './InternalLinkButton'
import ExternalLinkButton from './ExternalLinkButton'
import { tokenUrl } from '../../utils/linkGenerator'

const NavigationConnectButton = () => {
  const [isActivationModelOpen, setOpenActivationModal] = useState(false)
  const { subscriptionStatus } = useGlobalConfig()

  // We want to show the user they have connected their account
  if (subscriptionStatus === 'paid') {
    return (
      <InternalLinkButton type='ghost' label='Account Connected' icon='tick' href='/settings' />
    )
  }

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
        type='ghost'
        label='Connect Elements Account'
        icon='link'
        href={tokenUrl({ utm_content: 'get_started_button' })}
        openNewWindow
        onClick={() => {
          setOpenActivationModal(true)
        }}
      />
    </>
  )
}

export default NavigationConnectButton
