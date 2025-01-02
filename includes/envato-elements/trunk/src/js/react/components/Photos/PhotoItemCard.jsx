import React, { useRef, useState } from 'react'
import ButtonWrapper from '../Buttons/ButtonWrapper'
import Button from '../Buttons/Button'
import PhotoPreviewModal from '../Modal/PhotoPreviewModal'
import ImportPhotoButton from '../Buttons/ImportPhotoButton'
import styles from './PhotoItemCard.module.scss'
import useGlobalConfig from '../Contexts/useGlobalConfig'

const PhotoItemCard = ({ layout, item }) => {
  const { getDownloadedItemId } = useGlobalConfig()
  const [isActivationModelOpen, setOpenActivationModal] = useState(false)
  const cardRef = useRef(null)

  const isPhotoImported = !!getDownloadedItemId(item.humane_id)
  const imageUrl = item.cover_image_urls.w600 || item.cover_image_urls.w400 || item.cover_image_urls.w100
  const previewImageUrl = item.cover_image_urls.w1200 || item.cover_image_urls.w1000 || item.cover_image_urls.w600
  const backgroundImageInlineStyle = {
    backgroundImage: `url('${imageUrl}')`
  }
  const cardStyle = {}
  if (layout === 'masonry') {
    cardStyle.width = `${item.calculatedMasonryWidth}%`
    backgroundImageInlineStyle.paddingBottom = `${item.aspectRatioHeight}%`
  }
  return (
    <div className={`${layout === 'square' ? styles.itemSquare : styles.itemFluid}`} style={cardStyle}>
      <div className={styles.inner} style={backgroundImageInlineStyle}>
        <div className={styles.features}>
          {isPhotoImported ? <span className={styles.featureImported}>Imported</span> : null}
        </div>
        <div
          className={styles.details}
          ref={cardRef}
          onClick={(e) => {
            if (e.target === cardRef.current) {
              setOpenActivationModal(true)
            }
          }}
        >
          <div className={styles.detailsInner}>
            <div className={styles.title}>
              {item.title}
            </div>
            <ButtonWrapper>
              <ImportPhotoButton photoId={item.humane_id} photoTitle={item.title} showLabel={false} />
              {isActivationModelOpen
                ? (
                  <PhotoPreviewModal
                    photoUrl={previewImageUrl}
                    photoTitle={item.title}
                    photoId={item.humane_id}
                    aspectRatioHeight={item.aspectRatioHeight}
                    onCloseCallback={() => {
                      setOpenActivationModal(false)
                    }}
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
            </ButtonWrapper>
          </div>
        </div>
      </div>
    </div>
  )
}

export default PhotoItemCard
