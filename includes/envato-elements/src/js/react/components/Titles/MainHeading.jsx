import React from 'react'
import styles from './MainHeading.module.scss'

const MainHeading = ({ title, subtitle }) => (
  <div className={styles.wrapper}>
    <h1 className={styles.title}>{title}</h1>
    {subtitle ? <p className={styles.subtitle}>{subtitle}</p> : null}
  </div>
)

export default MainHeading
