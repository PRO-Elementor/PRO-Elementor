import React from 'react'
import MainHeading from '../../Titles/MainHeading'
import ProjectName from '../../Forms/ProjectName'

import styles from './TokenProjectSignup.module.scss'

const Step3ProjectName = ({ goToNextStep }) => (
  <div>
    <MainHeading title={(
      <>
        <span className={`dashicons dashicons-yes-alt ${styles.successIcon}`} />
        Success
      </>
    )}
    />
    <p className={styles.copy}>
      Your Token has been verified
    </p>
    <p className={styles.copy}>
      <strong>Your Project needs a name</strong> <br />
      All downloaded items will be licensed to this project name in Envato Elements:
    </p>
    <ProjectName completedCallback={goToNextStep} />
  </div>
)

export default Step3ProjectName
