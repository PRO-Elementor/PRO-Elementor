import React, { useState } from 'react'
import GridItem from '../Grid/GridItem'
import styles from './UploadKitCard.module.scss'
import { DoTheFileUpload } from '../Buttons/UploadTemplateKitButton'
import LoadingAnimation from '../Loading/LoadingAnimation'

const UploadKitCard = ({ item }) => {
  const [chosenFile, setChosenFile] = useState(null)
  return (
    <GridItem colWidthPercentage={33}>
      <div className={styles.uploadCard}>
        <label htmlFor='upload-template-kit-zip-file' className={styles.uploadCardButton}>
          {chosenFile
            ? <LoadingAnimation />
            : <span className={styles.icon} />}
          <div className={styles.message}>
            Upload Template Kit ZIP File
          </div>
          <input
            type='file'
            name='upload-template-kit-zip-file'
            id='upload-template-kit-zip-file'
            className={styles.formInput}
            onChange={e => {
              setChosenFile(e.target.files[0])
            }}
          />
        </label>
        {chosenFile
          ? (
            <DoTheFileUpload chosenFile={chosenFile} />
            )
          : null}
      </div>
    </GridItem>
  )
}

export default UploadKitCard
