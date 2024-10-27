import React, { useEffect, useState } from 'react'
import InputWithButton from './InputWithButton'
import verifyExtensionsToken from '../../api/verifyExtensionsToken'
import ButtonActionProvider from '../Actions/ButtonActionProvider'
import { tokenUrl } from '../../utils/linkGenerator'
import useGlobalConfig from '../Contexts/useGlobalConfig'
import Button from '../Buttons/Button'
import styles from './ExtensionsToken.module.scss'

/**
 * Generates a form allowing the user to input an Extensions API token
 *
 * @param trackingParams
 * @param customActionHook
 * @param completedCallback
 * @returns {*}
 * @constructor
 */
const ExtensionsToken = ({ trackingParams = {}, customActionHook = null, completedCallback = null }) => {
  const [token, setToken] = useState('')
  const [error, setError] = useState(null)
  const [saved, setSaved] = useState(false)
  const { setSubscriptionStatus } = useGlobalConfig()

  // Call our completedCallback() after our "saved" state variable is set to true:
  useEffect(() => {
    if (saved) {
      if (completedCallback) {
        completedCallback()
      }
    }
  }, [saved])

  useEffect(() => {
    // each time our local project name changes we update our saved state to false.
    setSaved(false)
    setError(null)
  }, [token])

  const tokenIsCorrectLength = token.length === 36

  return (
    <InputWithButton
      Input={(
        <input
          type='text'
          value={token}
          data-testid='token-input'
          onChange={e => {
            // Update local token state value:
            setToken(e.target.value)
          }}
          className={`${styles.input} ${tokenIsCorrectLength ? styles.success : ''}`}
          spellCheck='false'
          autoComplete='false'
          placeholder='Enter token here'
        />
      )}
      Button={(
        <ButtonActionProvider
          DefaultButton={<Button type={tokenIsCorrectLength ? 'primary' : 'ghost'} label='Verify Token' icon='arrow' dataTestId='elements-token-submit' />}
          LoadingButton={<Button type='ghost' label='Verifying...' icon='updateSpinning' disabled dataTestId='elements-token-submit' />}
          ErrorButton={<Button type='warning' label='Error' icon='cross' disabled dataTestId='elements-token-submit' />}
          SuccessButton={<Button type='ghost' label='Success!' icon='updateSpinning' disabled dataTestId='elements-token-submit' />}
          CompletedButton={<Button type='ghost' label='Success!' icon='updateSpinning' disabled dataTestId='elements-token-submit' />}
          actionHook={customActionHook || (() => verifyExtensionsToken({ token }))}
          isAlreadyCompleted={false}
          completedCallback={(data) => {
            if (data && data.status) {
              // Update global subscription state:
              setSubscriptionStatus(data.status)
            }
            setSaved(true)
          }}
          errorCallback={(data) => {
            setError(data && data.error
              ? data.error
              : {
                  code: 'unknown_error',
                  message: 'Sorry something went wrong, please try again.'
                })
          }}
        />
      )}
      instructions={(
        <p className={styles.copy}>
          <a href={tokenUrl({ trackingParams })} target='_blank' rel='noopener noreferrer'>
            Follow this link
          </a>{' '}
          to sign into Envato Elements and generate a token.
        </p>
      )}
      errorMessage={error ? error.message : null}
    />
  )
}

export default ExtensionsToken
