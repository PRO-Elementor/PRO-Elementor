import React from 'react'

import styles from './LoadingAnimation.module.scss'

const LoadingAnimation = () => (
  <div className={styles.wrap}>
    <span className={styles.inner} aria-label='Loading' />
  </div>
)

export default LoadingAnimation
