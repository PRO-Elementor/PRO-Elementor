import React, { useEffect, useState } from 'react'
import Button from '../Buttons/Button'
import ExternalLinkButton from '../Buttons/ExternalLinkButton'
import ImportElementorTemplateImages from '../Actions/ImportElementorTemplateImages'
import { getImportedTemplateUrl } from '../../utils/linkGenerator'
import getSingleTemplateImportData from '../../api/getSingleTemplateImportData'
import importSingleTemplate from '../../api/importSingleTemplate'
import useGlobalConfig from '../Contexts/useGlobalConfig'

const ImportTemplateInBackground = ({ templateKitId, templateId, importAgain, insertToPage, completeCallback }) => {
  const { loading, error, data } = importSingleTemplate({ templateKitId, templateId, importAgain, insertToPage })

  useEffect(() => {
    if (!loading && !error && data && data.imported_template_id) {
      // If we have finished loading (i.e. importing the template has finished)
      completeCallback(data)
    }
  }, [loading])

  return null
}

const GetTemplateData = ({ templateKitId, templateId, completeCallback }) => {
  const { loading, error, data } = getSingleTemplateImportData({ templateKitId, templateId })
  useEffect(() => {
    if (!loading && !error && data && data.template_data) {
      // We get some JSON back of the template we wish to import
      completeCallback(data.template_data)
    }
  }, [loading])

  return null
}

/**
 *
 * @param templateKitId
 * @param templateId
 * @param existingImports
 * @returns {*}
 * @constructor
 */
const ImportSingleTemplate = ({ templateKitId, templateId, existingImports = [] }) => {
  // Determines what type of mode we're in, this changes the button we choose to render on each templates kit.
  const { getMagicButtonMode } = useGlobalConfig()
  const magicButtonMode = getMagicButtonMode()
  const isElementorMagicMode = magicButtonMode && magicButtonMode.mode === 'elementorMagicButton'

  // Here we store the ID number of the latest imported template.
  // We use this ID further down to generate a URL to the installed template and show that in a button to the user
  const [importedTemplateId, setImportedTemplateId] = useState(isElementorMagicMode ? null : (existingImports.length ? existingImports[0].imported_template_id : null))
  // This is the JSON data for the Template we wish to import:
  const [templateDataToImport, setTemplateDataToImport] = useState(null)
  // This is the progress percent (stored as 0.4 for 40%) for the import process, reflected in the button label:
  const [importProgress, setImportProgress] = useState(0)
  // This is the import state we used to control what buttons are visible / what react hooks we mount in:
  const [importState, setImportState] = useState('idle')
  // This decides if we should import the templatea gain a second time.
  const [importTemplateAgain, setImportTemplateAgain] = useState(false)

  // This is the button the user clicks when they wish to start importing a template:
  const StartImportButton = importedTemplateId
    ? (
      <Button
        type='ghost' label='Import Again' icon='plus' onClick={() => {
          setImportProgress(0)
          setImportTemplateAgain(true)
          setImportState('importingFetchJsonData')
        }}
      />
      )
    : (
      <Button
        type='primary' label={isElementorMagicMode ? 'Insert Template' : 'Import Template'} icon='plus' onClick={() => {
          setImportProgress(0)
          setImportState('importingFetchJsonData')
        }}
      />
      )
  // This is the importing button we display to the user during the import process:
  const ImportingButton = <Button type='primary' label={`Importing ${Math.round(importProgress * 100)}%`} icon='updateSpinning' disabled />
  // This is the button that goes to imported template:
  const ImportedTemplateButton = <ExternalLinkButton href={getImportedTemplateUrl({ importedTemplateId })} type='primary' label='View Template' icon='eye' openNewWindow />

  return (
    <>
      {importedTemplateId ? ImportedTemplateButton : null}
      {importState === 'idle' ? StartImportButton : null}
      {importState === 'importingFetchJsonData' || importState === 'importingImages' || importState === 'importingTemplate' ? ImportingButton : null}

      {importState === 'importingFetchJsonData'
        ? (
          <GetTemplateData
            templateKitId={templateKitId}
            templateId={templateId}
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
            templateKitId={templateKitId}
            templateId={templateId}
            importAgain={importTemplateAgain}
            insertToPage={isElementorMagicMode}
            completeCallback={(data) => {
            // This means the final template import has been completed and we have a template ID ready to go
              if (data && data.imported_template_id) {
                setImportProgress(1)
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

export default ImportSingleTemplate
