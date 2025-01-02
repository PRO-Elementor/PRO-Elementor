import React, { useState } from 'react'
import useAPIError from './useAPIError'
import ModalWrapper from '../Modal/ModalWrapper'
import TokenProjectSignup from '../Modal/TokenProjectSignup'
import MainHeading from '../Titles/MainHeading'
import ResetUserSettings from '../Buttons/ResetUserSettings'

import styles from './APIErrorMessage.module.scss'
import Button from '../Buttons/Button'
import ButtonWrapper from '../Buttons/ButtonWrapper'

const APIErrorDebugInformation = ({ error }) => {
  const [showDebugInformation, setShowDebugInformation] = useState(false)

  return (
    <div className={styles.debugWrapper}>
      {error.debug && showDebugInformation
        ? (
          <div className={styles.debugInformation}>
            <textarea
              className={styles.debugText}
              onClick={(e) => {
                e.target.focus()
                e.target.select()
              }}
              defaultValue={
              typeof error.debug === 'object'
                ? JSON.stringify(error.debug, null, '\t')
                : error.debug
            }
            />
          </div>
          )
        : null}
      <div className={styles.debugActions}>
        <ButtonWrapper>
          <Button
            icon='update'
            label='Refresh Page'
            onClick={(e) => {
              e.preventDefault()
              window.location.reload()
              return false
            }}
          />
          {error.debug
            ? (
              <Button
                icon='eye'
                label={showDebugInformation ? 'Hide Debug Details' : 'Show Debug Details'}
                className={styles.buttonDebug}
                onClick={() => {
                  setShowDebugInformation(!showDebugInformation)
                }}
              />
              )
            : null}
          <ResetUserSettings />
        </ButtonWrapper>
        If this error continues please contact <a href='mailto:extensions@envato.com'>extensions@envato.com</a>.
      </div>
    </div>
  )
}

const APIErrorMessage = () => {
  const { errors, removeError } = useAPIError()

  const shouldOpenErrorModal = errors.length > 0

  if (!shouldOpenErrorModal) {
    return null
  }

  return (
    <>
      {errors.map(error => {
        if (error.code === 'invalid_subscription') {
          return (
            <TokenProjectSignup showWelcomeMessaging key={error.code} onCloseCallback={() => { removeError(error) }} />
          )
        }
        if (error.code === 'zip_failure') {
          return (
            <ModalWrapper key={error.code} isOpen onCloseCallback={() => { removeError(error) }}>
              <MainHeading title='Template Kit Install Error' />
              <p className={styles.copy}>There was an issue installing this template kit. Please try again.</p>
              {error.message}
              <APIErrorDebugInformation error={error} />
            </ModalWrapper>
          )
        }
        if (error.code === 'missing_permissions') {
          return (
            <ModalWrapper key={error.code} isOpen onCloseCallback={() => { removeError(error) }}>
              <MainHeading title='Error' />
              <p className={styles.copy}>
                {error.message}
              </p>
              <APIErrorDebugInformation error={error} />
            </ModalWrapper>
          )
        }
        if (error.code === 'generic_api_error') {
          return (
            <ModalWrapper key={error.code} isOpen onCloseCallback={() => { removeError(error) }}>
              <MainHeading title='Unexpected Error' />
              <p className={styles.copy}>Sorry there was an unexpected error from API call:</p>
              {error.message}
              <APIErrorDebugInformation error={error} />
            </ModalWrapper>
          )
        }
        return null
      })}
    </>
  )
}

export default APIErrorMessage
