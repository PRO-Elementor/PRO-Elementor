import React from 'react'
import importPhoto from '../../api/importPhoto'
import Button from './Button'
import ButtonActionProvider from '../Actions/ButtonActionProvider'
import ExternalLinkButton from './ExternalLinkButton'
import useGlobalConfig from '../Contexts/useGlobalConfig'
import { getImportedPhotoUrl } from '../../utils/linkGenerator'

/**
 * Helper to render a series of buttons to install a template kit.
 *
 * @param photoId
 * @param photoTitle
 * @param showLabel
 * @param customActionHook
 * @param completeCallback
 * @param errorCallback
 * @returns {*}
 * @constructor
 */
const ImportPhotoButton = ({ photoId, photoTitle, showLabel = false, customActionHook = null, completeCallback = null, errorCallback = null }) => {
  const { getDownloadedItemId, addDownloadedItem } = useGlobalConfig()
  const importedPhotoId = getDownloadedItemId(photoId)

  let isAlreadyCompleted = !!importedPhotoId

  if (typeof window.envatoElements.photoImportCompleteCallback !== 'undefined') {
    // This variable is set in "main.jsx" and lets us reach into WordPress core code.
    // If this is set then we treat the download action as incomplete, this means we fire
    // importPhoto() again when in Deep Photo Integration mode. This allows our API to
    // return the JSON required for deep integration to work correctly.
    // The photo isn't imported twice due to a check in PHP so all good.
    isAlreadyCompleted = false
  }

  return (
    <ButtonActionProvider
      DefaultButton={<Button type='primary' icon='download' label={showLabel ? 'Import Photo' : null} />}
      LoadingButton={<Button type='primary' icon='updateSpinning' label={showLabel ? 'Importing....' : null} disabled />}
      ErrorButton={<Button type='warning' icon='cross' label={showLabel ? 'Error!' : null} disabled />}
      SuccessButton={<Button type='primary' label={showLabel ? 'Success!' : null} icon='updateSpinning' disabled />}
      CompletedButton={<ExternalLinkButton type='primary' icon='eye' label={showLabel ? 'View Imported Photo' : null} href={getImportedPhotoUrl({ importedPhotoId })} />}
      actionHook={() => customActionHook ? customActionHook() : importPhoto({ photoId, photoTitle })}
      isAlreadyCompleted={isAlreadyCompleted}
      completedCallback={(data) => {
        if (data && data.success && data.imported_photo_id) {
          if (typeof window.envatoElements.photoImportCompleteCallback !== 'undefined') {
            window.envatoElements.photoImportCompleteCallback(data)
          }
          addDownloadedItem({
            humaneId: photoId,
            importedId: data.imported_photo_id
          })
        }
      }}
      errorCallback={errorCallback}
    />
  )
}

export default ImportPhotoButton
