import React, { useEffect, useState } from 'react'
import InputWithButton from './InputWithButton'
import ButtonActionProvider from '../Actions/ButtonActionProvider'
import savePreferredStartPage from '../../api/savePreferredStartPage'
import useGlobalConfig from '../Contexts/useGlobalConfig'
import Button from '../Buttons/Button'

import styles from './StartPage.module.scss'

const StartPageButton = ({ text, disabled = false, onClick = null }) => {
  return (
    <Button
      type='ghost'
      label={text}
      icon='arrow'
      disabled={disabled}
      onClick={onClick}
    />
  )
}

/**
 * Generates a form allowing the user to set their start page
 *
 * @param customActionHook
 * @param completedCallback
 * @returns {*}
 * @constructor
 */
const StartPage = ({ customActionHook = null, completedCallback = null }) => {
  const { getStartPage, setStartPage } = useGlobalConfig()
  const [stateStartPage, setStateStartPage] = useState(getStartPage())
  const [error, setError] = useState(null)
  const [saved, setSaved] = useState(false)

  // Call our completedCallback() after our "saved" state variable is set to true:
  useEffect(() => {
    if (saved) {
      setStartPage(stateStartPage)
      if (completedCallback) {
        completedCallback()
      }
    }
  }, [saved])

  useEffect(() => {
    // each time our local start page changes we update our saved state to false.
    setSaved(false)
    setError(null)
  }, [stateStartPage])

  const allowedStartPages = {
    welcome: 'Welcome Screen',
    'premium-kits': 'Premium Templates Kits',
    'free-kits': 'Free Template Kits',
    'installed-kits': 'Installed Template Kits',
    photos: 'Photos'
  }

  return (
    <InputWithButton
      Input={(
        <select
          onChange={e => {
            // Update local start page state value:
            setStateStartPage(e.target.value)
          }}
          value={stateStartPage}
          className={styles.select}
        >
          {Object.keys(allowedStartPages).map(key => (
            <option value={key} key={key}>{allowedStartPages[key]}</option>
          ))}
        </select>
      )}
      Button={(
        <ButtonActionProvider
          DefaultButton={<StartPageButton text='Update Start Page' />}
          LoadingButton={<StartPageButton text='Saving' disabled />}
          ErrorButton={<StartPageButton text='Error' disabled />}
          SuccessButton={<StartPageButton text='Success!' disabled />}
          CompletedButton={<StartPageButton text='Update Start Page' />}
          actionHook={customActionHook || (() => savePreferredStartPage({ startPage: stateStartPage }))}
          isAlreadyCompleted={saved}
          completedCallback={(data) => {
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
      instructions={null}
      errorMessage={error ? error.message : null}
    />
  )
}

export default StartPage
