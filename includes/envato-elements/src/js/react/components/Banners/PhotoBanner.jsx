import React, { useEffect, useState } from 'react'
import useGlobalConfig from '../Contexts/useGlobalConfig'
import styles from './PhotoBanner.module.scss'
import ActivateButton from '../Buttons/ActivateButton'
import DismissBannerButton, { isBannerAlreadyDismissed } from '../Buttons/DismissBanner'
import ExternalLinkButton from '../Buttons/ExternalLinkButton'
import MainHeading from '../Titles/MainHeading'
import ButtonWrapper from '../Buttons/ButtonWrapper'

const PhotoBanner = () => {
  const bannerId = 'photoBanner'
  const { subscriptionStatus } = useGlobalConfig()
  // Figure out if banner is already dismissed
  const alreadyDismissed = isBannerAlreadyDismissed(bannerId)

  // Determines what type of mode we're in, we don't want this banner to display in the embed photo mode.
  const isPhotoEmbedView = typeof window !== 'undefined' && typeof window.envatoElements !== 'undefined' && typeof window.envatoElements.photoImportCompleteCallback !== 'undefined'

  // Figure out if we should show the banner or not
  const shouldWeShowTheBanner = !alreadyDismissed && !isPhotoEmbedView && subscriptionStatus !== 'paid'
  // Use local state here to decide if banner should display or not
  const [showBanner, setShowBanner] = useState(shouldWeShowTheBanner)

  useEffect(() => {
    // If our subscription status is updated from elsewhere, we need to close this modal.
    if (subscriptionStatus === 'paid') {
      setShowBanner(false)
    }
  }, [subscriptionStatus])

  if (!showBanner) {
    // If our banner is dismissed we return nothing for this banner
    return null
  }

  return (
    <div className={styles.wrapper}>
      <div className={styles.col}>
        <MainHeading title='Premium Stock Photos from Envato Elements' />
        <div className={styles.text}>Browse over 1 Million Stock Photos from Envato Elements!<br />
          To download and import, you need to be a paid Envato Elements subscriber.
        </div>
        <ButtonWrapper>
          <ActivateButton />
          <ExternalLinkButton type='ghost' label='Find out more about Envato Elements' icon='arrow' href='https://elements.envato.com/?utm_source=extensions&utm_medium=referral&utm_campaign=photo_banner' openNewWindow='true' />
        </ButtonWrapper>
      </div>
      <div className={styles.col}>
        <div className={styles.colRight} />
      </div>
      <div className={styles.dismiss}>
        <DismissBannerButton
          bannerId={bannerId} completeCallback={() => {
            // Once our dismiss action has completed we update our local state to hide our banner.
            setShowBanner(false)
          }}
        />
      </div>
    </div>
  )
}

export default PhotoBanner
