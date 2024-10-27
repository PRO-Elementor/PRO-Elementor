import React from 'react'
import MainHeading from '../../Titles/MainHeading'
import ButtonWrapper from '../../Buttons/ButtonWrapper'
import ExternalLinkButton from '../../Buttons/ExternalLinkButton'
import { tokenUrl } from '../../../utils/linkGenerator'

import styles from './TokenProjectSignup.module.scss'

const Step1Welcome = ({ goToNextStep }) => (
  <>
    <MainHeading title='Envato Elements Subscription Required' />
    <div className={styles.downloadText}>To download and use this premium content, you<br /> need connect your Envato Elements subscription.</div>
    <div className={styles.unlimitedText}><strong>Unlimited Digital Assets</strong><br />
      One subscription, 2,100,000+ assets. <br />
      All the creative assets you need under one subscription.
    </div>
    <ButtonWrapper>
      <ExternalLinkButton
        type='primary'
        label='Get Started'
        icon='arrow'
        href={tokenUrl({ utm_content: 'get_started_button' })}
        openNewWindow
        onClick={goToNextStep}
      />
      <ExternalLinkButton type='ghost' icon='arrow' label='Find Out More' href='https://elements.envato.com/?utm_source=extensions&utm_medium=referral&utm_campaign=subscription_required_modal' openNewWindow />
    </ButtonWrapper>
    <div className={styles.backgroundImage} />
  </>
)

export default Step1Welcome
