import React from 'react'
import styles from './ItemCard.module.scss'

const ItemCard = ({ Images, Buttons, title, description }) => {
  return (
    <div className={styles.wrapper}>
      <div className={styles.inner}>
        <div className={styles.images}>
          {Images}
        </div>
        <div className={styles.meta}>
          <h4 className={styles.cardTitle}>{title}</h4>
          <p className={styles.cardDescription}>{description}</p>
          {Buttons}
        </div>
      </div>
    </div>
  )
}

export default ItemCard
