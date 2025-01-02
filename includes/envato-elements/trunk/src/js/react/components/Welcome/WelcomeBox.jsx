import React, { useState } from 'react'
import styles from './WelcomeBox.module.scss'
import InternalLinkButton from '../Buttons/InternalLinkButton'

const WelcomeBox = () => {
  const [playVideoEmbed, setPlayVideoEmbed] = useState(false)

  return (
    <div className={styles.wrapper}>
      <div className={styles.inner}>
        <div className={styles.contentWrapper}>
          <p className={styles.subHeading}>Welcome to the new and improved</p>
          <h1 className={styles.mainHeading}>Envato Elements WordPress Plugin</h1>
          <p className={styles.whatsNew}>
            <strong>What's new?</strong>{' '}
            Watch this video below to find out more
          </p>
          <div className={styles.videoWrapper} onClick={() => { setPlayVideoEmbed(true) }}>
            {playVideoEmbed ? <iframe className={styles.videoIframe} src='https://www.youtube.com/embed/XhZ1Rhrbu8g?rel=0&autoplay=1' /> : null}
          </div>
          <div className={styles.buttonWrapper}>
            <InternalLinkButton type='primary' label='Premium Template Kits' icon='arrow' href='/template-kits/premium-kits' />
            <InternalLinkButton type='primary' label='Free Template Kits' icon='arrow' href='/template-kits/free-kits' />
            <InternalLinkButton type='primary' label='Premium Photos' icon='arrow' href='/photos' />
            {/* <ExternalLinkButton
              type='primary' label={(
                <>
                  AI Generated Images
                  <span style={{
                    fontSize: '11px',
                    margin: '6px',
                    display: 'inline-block',
                    color: 'blue',
                    fontWeight: 'bold'
                  }}
                  >NEW
                  </span>
                </>
            )} icon='link' href='https://labs.envato.com/?utm_source=extensions&utm_medium=referral&utm_campaign=wp-elements-home'
            /> */}
          </div>
        </div>
      </div>
    </div>
  )
}

export default WelcomeBox
