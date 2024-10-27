import React from 'react'
import installTemplateKit from '../../api/installPremiumTemplateKit'
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
const InstallPremiumTemplateKitButton = ({ templateKitId, importedTemplateKitId, customActionHook = null, completeCallback = null, errorCallback = null }) => {
  const { addDownloadedItem } = useGlobalConfig()
  return (
    <ButtonActionProvider
      DefaultButton={<Button type='primary' label='Install Kit' icon='plus' />}
      LoadingButton={<Button type='primary' label='Installing' icon='updateSpinning' disabled />}
      ErrorButton={<Button type='warning' label='Error' icon='cross' disabled />}
      SuccessButton={<Button type='primary' label='Success!' icon='plus' disabled />}
      CompletedButton={<InternalLinkButton label='View Kit' type='primary' icon='eye' href={getImportedKitUrl({ importedTemplateKitId })} />}
      actionHook={() => customActionHook ? customActionHook() : installTemplateKit({ templateKitId })}
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

export default InstallPremiumTemplateKitButton
