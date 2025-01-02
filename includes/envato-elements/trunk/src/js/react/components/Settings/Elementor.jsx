import React, { useEffect, useState } from 'react'

import Button from '../Buttons/Button'
import InputWithButton from '../Forms/InputWithButton'
import ButtonActionProvider from '../Actions/ButtonActionProvider'
import getElementorGlobalStyleTemplates from '../../api/getElementorGlobalStyleTemplates'
import saveElementorGlobalStyleTemplate from '../../api/saveElementorGlobalStyleTemplate'
import LoadingAnimation from '../Loading/LoadingAnimation'

import styles from './Elementor.module.scss'

const SaveElementorGlobalStylesButton = ({ text, disabled = false, onClick = null }) => {
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

const Elementor = () => {
  const { loading: availableGlobalTemplatesLoading, data: availableGlobalTemplates } = getElementorGlobalStyleTemplates()
  const [stateGlobalStyleTemplate, setStateGlobalStyleTemplate] = useState(null)
  const [globalStyleTemplateError, setGlobalStyleTemplateError] = useState(null)
  const [globalStyleTemplateSaved, setGlobalStyleTemplateSaved] = useState(false)

  useEffect(() => {
    // each time our local state changes we update our saved state to false.
    setGlobalStyleTemplateSaved(false)
    setGlobalStyleTemplateError(null)
  }, [stateGlobalStyleTemplate])

  useEffect(() => {
    if (availableGlobalTemplates) {
      const defaultTemplate = availableGlobalTemplates.find(template => template.default)
      if (defaultTemplate) {
        setStateGlobalStyleTemplate(defaultTemplate.id)
      }
    }
  }, [availableGlobalTemplates])

  return (
    <>
      <p className={styles.copy}>
        Some Template Kits include "Global Site Settings", if applied these can effect your entire website. Site Settings can be modified from the Elementor Hamburger Menu &raquo; Site Settings. You can change which global site settings are applied to your website below.
      </p>
      {
        (availableGlobalTemplatesLoading || !availableGlobalTemplates.length)
          ? (
            <LoadingAnimation />
            )
          : (
            <InputWithButton
              Input={(
                <select
                  onChange={e => {
                    // Update local setting state value:
                    setStateGlobalStyleTemplate(e.target.value)
                  }}
                  value={stateGlobalStyleTemplate}
                  className={styles.select}
                >
                  <option value=''>Reset to Default</option>
                  {availableGlobalTemplates.map(template => (
                    <option value={template.id} key={template.id}>{template.title}</option>
                  ))}
                </select>
          )}
              Button={(
                <ButtonActionProvider
                  DefaultButton={<SaveElementorGlobalStylesButton text='Update Global Template' />}
                  LoadingButton={<SaveElementorGlobalStylesButton text='Saving' disabled />}
                  ErrorButton={<SaveElementorGlobalStylesButton text='Error' disabled />}
                  SuccessButton={<SaveElementorGlobalStylesButton text='Success!' disabled />}
                  CompletedButton={<SaveElementorGlobalStylesButton text='Update Global Template' />}
                  actionHook={() => saveElementorGlobalStyleTemplate({ globalStyleTemplateId: stateGlobalStyleTemplate })}
                  isAlreadyCompleted={globalStyleTemplateSaved}
                  completedCallback={(data) => {
                    setGlobalStyleTemplateSaved(true)
                  }}
                  errorCallback={(data) => {
                    setGlobalStyleTemplateError(data && data.error
                      ? data.error
                      : {
                          code: 'unknown_error',
                          message: 'Sorry something went wrong, please try again.'
                        })
                  }}
                />
          )}
              instructions={null}
              errorMessage={globalStyleTemplateError ? globalStyleTemplateError.message : null}
            />
            )
}
    </>
  )
}

export default Elementor
