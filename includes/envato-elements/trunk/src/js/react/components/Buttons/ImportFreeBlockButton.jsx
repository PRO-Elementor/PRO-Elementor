import React, { useEffect, useState } from 'react'
import Button from './Button'
import useGlobalConfig from '../Contexts/useGlobalConfig'
import ExternalLinkButton from './ExternalLinkButton'
import ImportElementorTemplateImages from '../Actions/ImportElementorTemplateImages'
import { getImportedTemplateUrl } from '../../utils/linkGenerator'
import importFreeBlock from '../../api/importFreeBlock'

const ImportTemplateInBackground = ({ blockId, jsonUrl, importAgain, insertToPage, completeCallback }) => {
  const { loading, error, data } = importFreeBlock({ blockId, jsonUrl, importAgain, insertToPage })

  useEffect(() => {
    if (!loading && !error && data && data.imported_template_id) {
      // If we have finished loading (i.e. importing the template has finished)
      completeCallback(data)
    }
  }, [loading])

  return null
}

const GetTemplateData = ({ jsonUrl, completeCallback }) => {
  const [jsonData, setJsonData] = useState(null)

  useEffect(() => {
    if (jsonData) {
      completeCallback(jsonData)
    }
  }, [jsonData])

  useEffect(() => {
    fetch(jsonUrl, {
      method: 'get'
    }).then(response => {
      response.json().then(responseJson => {
        setJsonData(responseJson)
      })
    })
  }, [])

  return null
}

/**
 * Helper to render a series of buttons to install a template kit.
 *
 * @param blockId
 * @param jsonUrl
 * @param importedBlockId
 * @returns {*}
 * @constructor
 */
const ImportFreeBlockButton = ({ blockId, jsonUrl, importedBlockId }) => {
  // Determines what type of mode we're in, this changes the button we choose to render on each templates kit.
  const { addDownloadedItem, getMagicButtonMode } = useGlobalConfig()
  const magicButtonMode = getMagicButtonMode()
  const isElementorMagicMode = magicButtonMode && magicButtonMode.mode === 'elementorMagicButton'

  // Here we store the ID number of the latest imported template.
  // We use this ID further down to generate a URL to the installed template and show that in a button to the user
  const [importedTemplateId, setImportedTemplateId] = useState(importedBlockId)
  // This is the JSON data for the Template we wish to import:
  const [templateDataToImport, setTemplateDataToImport] = useState(null)
  // This is the progress percent (stored as 0.4 for 40%) for the import process, reflected in the button label:
  const [importProgress, setImportProgress] = useState(0)
  // This is the import state we used to control what buttons are visible / what react hooks we mount in:
  const [importState, setImportState] = useState('idle')
  // This decides if we should import the templatea gain a second time.
  const [importTemplateAgain, setImportTemplateAgain] = useState(false)

  // This is the button the user clicks when they wish to start importing a template:
  let StartImportButton = null
  if (isElementorMagicMode) {
    // We're in magic button mode. Always show a 'insert template' button even if already imported before
    StartImportButton = (
      <Button
        type='primary'
        label='Insert Template'
        icon='plus'
        onClick={() => {
          setImportProgress(0)
          setImportState('importingFetchJsonData')
        }}
      />
    )
  } else if (importedTemplateId) {
    // We've imported this blog before, show an import again button
    StartImportButton = (
      <Button
        type='ghost'
        label='Import Again'
        icon='plus'
        onClick={() => {
          setImportProgress(0)
          setImportTemplateAgain(true)
          setImportState('importingFetchJsonData')
        }}
      />
    )
  } else {
    StartImportButton = (
      <Button
        type='primary'
        label='Import Template'
        icon='plus'
        onClick={() => {
          setImportProgress(0)
          setImportState('importingFetchJsonData')
        }}
      />
    )
  }
  // This is the importing button we display to the user during the import process:
  const ImportingButton = <Button type='primary' label={`Importing ${Math.round(importProgress * 100)}%`} icon='updateSpinning' disabled />
  // This is the button that goes to imported template:
  const ImportedTemplateButton = <ExternalLinkButton href={getImportedTemplateUrl({ importedTemplateId })} type='primary' label='View Template' icon='eye' openNewWindow />

  return (
    <>
      {importedTemplateId && !isElementorMagicMode ? ImportedTemplateButton : null}
      {importState === 'idle' ? StartImportButton : null}
      {importState === 'importingFetchJsonData' || importState === 'importingImages' || importState === 'importingTemplate' ? ImportingButton : null}

      {importState === 'importingFetchJsonData'
        ? (
          <GetTemplateData
            jsonUrl={jsonUrl}
            completeCallback={(data) => {
            // This means we've collected the Elementor JSOn data correctly, time to import these images:
              setTemplateDataToImport(data)
              setImportState('importingImages')
            }}
          />
          )
        : null}
      {importState === 'importingImages'
        ? (
          <ImportElementorTemplateImages
            templateData={templateDataToImport}
            progressCallback={(importProgress) => {
              setImportProgress(importProgress)
            }}
            completeCallback={() => {
            // This means we've imported the template images correctly, time to fire off the final json import.
              setImportState('importingTemplate')
            }}
          />
          )
        : null}
      {importState === 'importingTemplate'
        ? (
          <ImportTemplateInBackground
            blockId={blockId}
            jsonUrl={jsonUrl}
            importAgain={importTemplateAgain}
            insertToPage={isElementorMagicMode}
            completeCallback={(data) => {
            // This means the final template import has been completed and we have a template ID ready to go
              if (data && data.imported_template_id) {
                setImportProgress(1)
                addDownloadedItem({ humaneId: blockId, importedId: data.imported_template_id })
                setTimeout(() => {
                  setImportedTemplateId(data.imported_template_id)
                  setImportState('idle')
                }, 300)

                // Fire off the magic button insert callback if it exists
                if (isElementorMagicMode && magicButtonMode.insertCallback && typeof magicButtonMode.insertCallback === 'function') {
                  magicButtonMode.insertCallback(data)
                }
              }
            }}
          />
          )
        : null}
    </>
  )
}

export default ImportFreeBlockButton
