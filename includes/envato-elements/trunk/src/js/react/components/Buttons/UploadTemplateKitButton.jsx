import React, { useState } from 'react'
import styles from './UploadTemplateKitButton.module.scss'
import uploadTemplateKitZipFile from '../../api/uploadTemplateKitZipFile'
import { useHistory } from 'react-router-dom'
import ButtonIconAndLabel from './ButtonIconAndLabel'
import ButtonElement from './ButtonElement'
import { getImportedKitUrl } from '../../utils/linkGenerator'

export const DoTheFileUpload = ({ chosenFile }) => {
  const history = useHistory()
  const { loading, data, error } = uploadTemplateKitZipFile({ file: chosenFile })

  if (!loading && !error && data && data.templateKitId) {
    history.push(getImportedKitUrl({ importedTemplateKitId: data.templateKitId }))
  }

  return null
}

const UploadTemplateKitButton = () => {
  const [chosenFile, setChosenFile] = useState(null)

  return (
    <>
      <ButtonElement element='label' htmlFor='upload-template-kit-zip-file'>
        <ButtonIconAndLabel label={chosenFile ? 'Processing...' : 'Upload Template Kit (Zip File)'} icon='link' />
        <input
          type='file'
          name='upload-template-kit-zip-file'
          id='upload-template-kit-zip-file'
          className={styles.formInput}
          onChange={e => {
            setChosenFile(e.target.files[0])
          }}
        />
      </ButtonElement>
      {chosenFile
        ? (
          <DoTheFileUpload chosenFile={chosenFile} />
          )
        : null}
    </>
  )
}

export default UploadTemplateKitButton
