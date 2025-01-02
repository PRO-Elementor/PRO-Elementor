import React, { useState } from 'react'
import { useParams } from 'react-router-dom'
import TemplateModalWrapper from './TemplateModalWrapper'
import MissingRequirements from '../Banners/MissingRequirements'
import fetchIndividualTemplates from '../../api/fetchIndividualTemplates'
import styles from './TemplatePreviewModal.module.scss'

const TemplatePreviewModal = ({ onCloseCallback, templateId, templateKitId, existingImports, templateScreenShotUrl, templatePreviewTitle, installRequirements }) => {
  // This gets the Installed Template Kit ID number from the URL string:
  const { id } = useParams()
  // We use a 'refresh' timestamp to allow easy page reloading. We just have to setRefresh(timestamp) and the
  // data on this page will get a refresh from the server. We do a data refresh after any missing requirements
  // are installed in the modal.
  const [refresh, setRefresh] = useState(null)
  // This gets the Installed Kit details from an API call:
  const installedKit = fetchIndividualTemplates({ id, refresh })

  return (
    <TemplateModalWrapper templateId={templateId} templateKitId={templateKitId} existingImports={existingImports} templatePreviewTitle={templatePreviewTitle} isOpen onCloseCallback={onCloseCallback}>
      {
        (installRequirements && !installedKit.loading && !installedKit.error && installedKit.data)
          ? (
            <div className={styles.missingRequirementsWrapper}>
              <MissingRequirements
                settings={installedKit.data.requirements.settings}
                theme={installedKit.data.requirements.theme}
                plugins={installedKit.data.requirements.plugins}
                requiredCss={installedKit.data.requirements.css}
                templateKitId={id}
                completeCallback={() => {
                  // When missing requirements are actioned we get a callback here.
                  // We set a new refresh value which results in a new API call and a complete data refresh of this page
                  // (which includes updated state on missing plugin requirements from our vendored import code)
                  setRefresh(new Date().getTime())
                }}
              />
            </div>
            )
          : null
}
      <img className={styles.previewTemplate} src={templateScreenShotUrl} alt={templatePreviewTitle} />
    </TemplateModalWrapper>
  )
}

export default TemplatePreviewModal
