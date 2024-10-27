import React, { useRef, useState } from 'react'
import GridItem from '../Grid/GridItem'
import ItemCard from '../Card/ItemCard'
import ButtonWrapper from '../Buttons/ButtonWrapper'
import Button from '../Buttons/Button'
import ImportSingleTemplate from '../Buttons/ImportSingleTemplate'
import TemplatePreviewModal from '../Modal/TemplatePreviewModal'
import styles from './TemplateItemCardWrapper.module.scss'

const TemplateItemCardWrapper = ({ template }) => {
  const [isActivationModelOpen, setOpenActivationModal] = useState(false)
  const cardRef = useRef(null)
  const templateId = template.id
  const templateKitId = template.template_kit_id
  const templateName = template.name
  const templateScreenShotUrl = template.screenshot_url
  const templateExistingImports = template.imports

  const [blockImport, setBlockImport] = useState(template.unmet_requirements && template.unmet_requirements.length > 0)

  const description = template.metadata.additional_template_information ? template.metadata.additional_template_information.join(' ') : ''

  return (
    <GridItem colWidthPercentage={33} key={templateId}>
      <ItemCard
        Images={(
          <div className={styles.imageWrapper}>
            <img src={templateScreenShotUrl} alt={templateName} className={styles.image} />
            <div
              className={styles.expandButton}
              ref={cardRef}
              onClick={(e) => {
                if (e.target === cardRef.current) {
                  setOpenActivationModal(true)
                }
              }}
            >
              {isActivationModelOpen
                ? (
                  <TemplatePreviewModal
                    templateScreenShotUrl={templateScreenShotUrl}
                    templatePreviewTitle={templateName}
                    templateKitId={templateKitId}
                    templateId={templateId}
                    existingImports={templateExistingImports}
                    onCloseCallback={() => {
                      setOpenActivationModal(false)
                    }}
                    installRequirements={blockImport}
                  />
                  )
                : null}
              <Button
                type='ghost'
                icon='expand'
                onClick={() => {
                  setOpenActivationModal(true)
                }}
              />
            </div>
          </div>
        )}
        Buttons={(
          <>
            {blockImport
              ? (
                <>
                  <p className={styles.unmetRequirementsMessage}>
                    {template.unmet_requirements.join(' ')}
                  </p>
                  <Button type='warning' label='Ignore Requirements' icon='cross' onClick={() => setBlockImport(false)} />
                </>
                )
              : (
                <ButtonWrapper>
                  <ImportSingleTemplate
                    templateKitId={templateKitId}
                    templateId={templateId}
                    existingImports={templateExistingImports}
                  />
                </ButtonWrapper>
                )}
          </>
        )}
        title={templateName}
        description={description}
      />
    </GridItem>
  )
}

export default TemplateItemCardWrapper
