import React from 'react'
import dismissBanner from '../../api/dismissBanner'
import ButtonActionProvider from '../Actions/ButtonActionProvider'
import useGlobalConfig from '../Contexts/useGlobalConfig'
import styles from './DismissBanner.module.scss'

/**
 * Figure out if a banner was already dismissed by the user.
 *
 * @param bannerId
 * @returns {*}
 */
export const isBannerAlreadyDismissed = (bannerId) => {
  const { isBannerDismissed } = useGlobalConfig()
  return isBannerDismissed(bannerId)
}

/**
 * Render a close banner button
 *
 * @param bannerId
 * @param completeCallback
 * @returns {*}
 * @constructor
 */
const DismissBannerButton = ({ bannerId, completeCallback }) => {
  const CloseButton = <button data-testid='modal-close-button'><span className={`dashicons dashicons-no-alt ${styles.dismissButton}`} /></button>
  const { bannerHasBeenDismissed } = useGlobalConfig()

  return (
    <ButtonActionProvider
      DefaultButton={CloseButton}
      LoadingButton={CloseButton}
      ErrorButton={CloseButton}
      SuccessButton={CloseButton}
      CompletedButton={CloseButton}
      actionHook={() => dismissBanner({ bannerId })}
      isAlreadyCompleted={false}
      completedCallback={() => {
        bannerHasBeenDismissed(bannerId)
        if (completeCallback) {
          completeCallback()
        }
      }}
    />
  )
}

export default DismissBannerButton
