import React from 'react'
import resetUserSettings from '../../api/resetUserSettings'
import ButtonActionProvider from '../Actions/ButtonActionProvider'
import Button from '../Buttons/Button'

/**
 * Helper to reset the users settings.
 *
 * @param customActionHook
 * @param completeCallback
 * @param errorCallback
 * @returns {*}
 * @constructor
 */
const ResetUserSettings = ({ customActionHook = null, completeCallback = null, errorCallback = null }) => {
  return (
    <ButtonActionProvider
      actionConfirmationMessage='Really reset all plugin settings and data?'
      DefaultButton={<Button type='ghost' label='Clear Cache &amp; Reset Plugin' icon='update' />}
      LoadingButton={<Button type='ghost' label='Resetting...' icon='updateSpinning' disabled />}
      ErrorButton={<Button type='ghost' label='Something went wrong!' icon='update' disabled />}
      SuccessButton={<Button type='ghost' label='Resetting...' icon='updateSpinning' disabled />}
      CompletedButton={<Button type='ghost' label='Resetting...' icon='updateSpinning' />}
      actionHook={() => customActionHook ? customActionHook() : resetUserSettings()}
      isAlreadyCompleted={false}
      completedCallback={() => {
        if (completeCallback) {
          completeCallback()
        }
        window.location.reload()
      }}
      errorCallback={errorCallback}
    />
  )
}

export default ResetUserSettings
