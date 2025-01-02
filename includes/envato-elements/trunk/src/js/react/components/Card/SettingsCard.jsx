import React from 'react'
import MainHeading from '../Titles/MainHeading'

import styles from './SettingsCard.module.scss'

const SettingsCard = ({ title, children }) => (
  <div className={styles.wrapper}>
    <MainHeading title={title} />
    {children}
  </div>
)

export default SettingsCard
