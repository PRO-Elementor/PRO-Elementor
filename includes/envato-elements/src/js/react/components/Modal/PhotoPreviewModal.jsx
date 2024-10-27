import React from 'react'
import PhotoModalWrapper from './PhotoModalWrapper'
import styles from './PhotoPreviewModal.module.scss'

const PhotoPreviewModal = ({ onCloseCallback, photoUrl, photoTitle, photoId, aspectRatioHeight }) => {
  return (
    <PhotoModalWrapper photoTitle={photoTitle} photoId={photoId} isOpen onCloseCallback={onCloseCallback}>
      <div className={styles.loadingImageWrapper} style={{ paddingBottom: `${aspectRatioHeight}%` }}>
        <img className={styles.previewPhoto} src={photoUrl} alt={photoTitle} />
      </div>
    </PhotoModalWrapper>
  )
}

export default PhotoPreviewModal
