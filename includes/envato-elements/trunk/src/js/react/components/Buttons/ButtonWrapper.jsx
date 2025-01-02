import React from 'react'
import styles from './ButtonWrapper.module.scss'

const ButtonWrapper = ({ children }) => (
  <div className={styles.wrapper}>
    {children}
  </div>
)

export default ButtonWrapper
