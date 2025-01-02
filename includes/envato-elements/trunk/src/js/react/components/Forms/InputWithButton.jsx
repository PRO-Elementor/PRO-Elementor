import React from 'react'
import styles from './InputWithButton.module.scss'

const InputWithButton = ({ Input, Button, instructions, errorMessage }) => (
  <div className={styles.wrapper}>
    <div className={styles.formWrapper}>
      <div className={styles.inputWrapper}>
        {Input}
      </div>
      <div className={styles.buttonWrapper}>
        {Button}
      </div>
    </div>
    {errorMessage
      ? (
        <div className={styles.errors}>
          {errorMessage}
        </div>
        )
      : null}
    {instructions
      ? (
        <div className={styles.instructions}>
          {instructions}
        </div>
        )
      : null}
  </div>
)

export default InputWithButton
