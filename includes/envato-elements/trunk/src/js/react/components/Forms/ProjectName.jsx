import React, { useEffect, useState } from 'react'
import InputWithButton from './InputWithButton'
import ButtonActionProvider from '../Actions/ButtonActionProvider'
import setProjectName from '../../api/setProjectName'
import useGlobalConfig from '../Contexts/useGlobalConfig'
import Button from '../Buttons/Button'

import styles from './ProjectName.module.scss'

const ProjectButton = ({ text, disabled = false, onClick = null }) => {
  return (
    <Button
      type='ghost'
      label={text}
      icon='arrow'
      disabled={disabled}
      onClick={onClick}
      dataTestId='project-name-submit'
    />
  )
}

/**
 * Generates a form allowing the user to set their project name
 *
 * @param customActionHook
 * @param completedCallback
 * @returns {*}
 * @constructor
 */
const ProjectName = ({ customActionHook = null, completedCallback = null }) => {
  const { getConfigProjectName, setConfigProjectName } = useGlobalConfig()
  const [stateProjectName, setStateProjectName] = useState(getConfigProjectName())
  const [error, setError] = useState(null)
  const [saved, setSaved] = useState(false)

  // Call our completedCallback() after our "saved" state variable is set to true:
  useEffect(() => {
    if (saved) {
      setConfigProjectName(stateProjectName)
      if (completedCallback) {
        completedCallback()
      }
    }
  }, [saved])

  useEffect(() => {
    // each time our local project name changes we update our saved state to false.
    setSaved(false)
    setError(null)
  }, [stateProjectName])

  return (
    <InputWithButton
      Input={(
        <input
          type='text'
          value={stateProjectName}
          data-testid='project-name-input'
          onChange={e => {
            // Update local project name state value:
            setStateProjectName(e.target.value)
          }}
          className={styles.input}
          spellCheck='false'
          autoComplete='false'
          placeholder='My New Project'
        />
      )}
      Button={(
        <ButtonActionProvider
          DefaultButton={<ProjectButton text='Update Project Name' />}
          LoadingButton={<ProjectButton text='Saving' disabled />}
          ErrorButton={<ProjectButton text='Error' disabled />}
          SuccessButton={<ProjectButton text='Success!' disabled />}
          CompletedButton={<ProjectButton text='Update Project Name' />}
          actionHook={customActionHook || (() => setProjectName({ projectName: stateProjectName }))}
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

export default ProjectName
