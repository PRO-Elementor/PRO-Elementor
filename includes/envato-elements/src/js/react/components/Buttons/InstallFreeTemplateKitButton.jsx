import React from 'react'
import installFreeTemplateKit from '../../api/installFreeTemplateKit'
import Button from './Button'
import InternalLinkButton from './InternalLinkButton'
import ButtonActionProvider from '../Actions/ButtonActionProvider'
import { getImportedKitUrl } from '../../utils/linkGenerator'
import useGlobalConfig from '../Contexts/useGlobalConfig'

/**
 * Helper to render a series of buttons to install a template kit.
 *
 * @param templateKitId
 * @param importedTemplateKitId
 * @param customActionHook
 * @param completeCallback
 * @param errorCallback
 * @returns {*}
 * @constructor
 */
const InstallFreeTemplateKitButton = ({ zipUrl, templateKitId, importedTemplateKitId, customActionHook = null, completeCallback = null, errorCallback = null }) => {
  const { addDownloadedItem } = useGlobalConfig()
  return (
    <ButtonActionProvider
      DefaultButton={<Button type='primary' label='Install Kit' icon='plus' />}
      LoadingButton={<Button type='primary' label='Installing' icon='updateSpinning' disabled />}
      ErrorButton={<Button type='warning' label='Error' icon='cross' disabled />}
      SuccessButton={<Button type='primary' label='Success!' icon='plus' disabled />}
      CompletedButton={<InternalLinkButton type='primary' label='View Kit' icon='eye' href={getImportedKitUrl({ importedTemplateKitId })} />}
      actionHook={() => customActionHook ? customActionHook() : installFreeTemplateKit({ zipUrl, templateKitId })}
      isAlreadyCompleted={!!importedTemplateKitId}
      completedCallback={(data) => {
        if (data && data.success && data.template_kit_id) {
          addDownloadedItem({ humaneId: templateKitId, importedId: data.template_kit_id })
        }
        if (completeCallback) {
          completeCallback(data)
        }
      }}
      errorCallback={errorCallback}
    />
  )
}

export default InstallFreeTemplateKitButton
