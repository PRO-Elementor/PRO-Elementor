import React from 'react'
import MainHeading from '../../Titles/MainHeading'
import Button from '../../Buttons/Button'

import styles from './TokenProjectSignup.module.scss'

const Step4Connected = ({ closeModal }) => (
  <div>
    <MainHeading title={(
      <span className={styles.connectedTitle}>
        <svg className={styles.envatoLogo} width='25' height='28' viewBox='0 0 25 28' fill='none' xmlns='http://www.w3.org/2000/svg'>
          <path d='M21.9598 1.18968C18.0589 -3.24393 5.45609 5.35184 5.54921 16.4308C5.54921 16.5668 5.49361 16.6972 5.39465 16.7934C5.29569 16.8896 5.16146 16.9436 5.02151 16.9436C4.92987 16.9428 4.83995 16.9193 4.76028 16.8753C4.6806 16.8314 4.61381 16.7683 4.56623 16.6922C3.83042 15.1412 3.44917 13.4537 3.44874 11.7459C3.46175 10.463 3.6853 9.1903 4.11096 7.97582C4.1447 7.8713 4.14084 7.75874 4.10002 7.65664C4.0592 7.55453 3.98384 7.46896 3.88632 7.41399C3.78881 7.35901 3.67496 7.33791 3.56346 7.35414C3.45197 7.37037 3.34948 7.42297 3.27283 7.5033C1.66754 9.1766 0.595346 11.2658 0.185822 13.5184C-0.223701 15.7711 0.046972 18.0908 0.965133 20.1972C1.88329 22.3035 3.40965 24.1065 5.35974 25.3881C7.30984 26.6698 9.60022 27.3753 11.9541 27.4194H12.2335C29.4098 27.0273 25.4365 5.15078 21.9598 1.18968Z' />
        </svg>
        Connected
      </span>
    )}
    />
    <p className={styles.copy}>
      You're all connected and ready to start importing premium Template Kits & Photos.
    </p>
    <Button onClick={closeModal} type='primary' label='Get Started' icon='arrow' dataTestId='complete-signup-wizard' />
  </div>
)

export default Step4Connected
