import React from 'react'
import MainHeading from '../../Titles/MainHeading'
import ExtensionsToken from '../../Forms/ExtensionsToken'
import styles from './TokenProjectSignup.module.scss'

const Step2Token = ({ goToNextStep }) => (
  <div>
    <MainHeading title='Connect your Envato Elements subscription' />
    <p className={styles.copy}>
      <strong>Verify your Envato Elements Subscription</strong> <br />
      Enter your Token below to verify your Subscription:
    </p>
    <ExtensionsToken completedCallback={goToNextStep} />
  </div>
)

export default Step2Token
