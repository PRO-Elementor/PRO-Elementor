import React from 'react'
import styles from './ModalEnvatoIcon.module.scss'

const ModalEnvatoIcon = () => {
  return (
    <div className={styles.svgWrapper}>
      <svg
        className={styles.svgEnvatoLogo}
        xmlns='http://www.w3.org/2000/svg'
        width='14'
        height='14'
        fill='none'
        viewBox='0 0 27 31'
      >
        <path
          fill='#fff'
          d='M23.64 1.318C19.45-3.592 5.89 5.918 6 18.178a.58.58 0 01-.57.57.58.58 0 01-.49-.28 13.13 13.13 0 01-.52-9.65.53.53 0 00-.9-.52A13 13 0 000 17.188a13 13 0 0013.15 13.15c18.5-.42 14.23-24.64 10.49-29.02z'
        />
      </svg>
    </div>
  )
}

export default ModalEnvatoIcon
