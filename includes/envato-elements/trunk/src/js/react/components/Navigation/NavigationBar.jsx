import React from 'react'
import { Link, useRouteMatch } from 'react-router-dom'
import NavigationConnectButton from '../Buttons/NavigationConnectButton'

import styles from './NavigationBar.module.scss'

const NavigationBar = () => {
  return (
    <div className={styles.wrapper}>
      <div className={styles.logo}>
        <Link to='/welcome' className={styles.logoLink}>
          Envato Elements
        </Link>
      </div>

      <nav className={styles.menu}>
        <ul className={styles.menuInner}>
          <li
            className={`${styles.menuItem} ${styles.menuItemHasChild}`}
          >
            <Link
              to='/template-kits'
              className={`${styles.menuLink} ${useRouteMatch({ path: '/template-kits' }) ? styles.menuLinkActive : ''}`}
            >
              Template Kits
            </Link>
            <ul className={styles.subNavWrap}>
              <li className={styles.subNavItem}>
                <Link
                  to='/template-kits'
                  className={`${styles.menuLink} ${useRouteMatch({
                    path: '/template-kits/premium-kits*'
                  })
                    ? styles.menuLinkActive
                    : ''}`}
                >
                  Premium Kits
                </Link>
              </li>
              <li className={styles.subNavItem}>
                <Link
                  to='/template-kits/free-kits'
                  className={`${styles.menuLink} ${useRouteMatch({
                    path: '/template-kits/free-kits*'
                  })
                    ? styles.menuLinkActive
                    : ''}`}
                >
                  Free Kits
                </Link>
              </li>
              <li className={styles.subNavItem}>
                <Link
                  to='/template-kits/free-blocks'
                  className={`${styles.menuLink} ${useRouteMatch({
                    path: '/template-kits/free-blocks*'
                  })
                    ? styles.menuLinkActive
                    : ''}`}
                >
                  Free Blocks
                </Link>
              </li>
              <li className={styles.subNavItem}>
                <Link
                  to='/template-kits/installed-kits'
                  className={`${styles.menuLink} ${useRouteMatch({
                    path: '/template-kits/installed-kits*'
                  })
                    ? styles.menuLinkActive
                    : ''}`}
                >
                  Installed Kits
                </Link>
              </li>
            </ul>
          </li>
          <li
            className={`${styles.menuItem}`}
          >
            <Link
              to='/photos'
              className={`${styles.menuLink} ${useRouteMatch({ path: '/photos' }) ? styles.menuLinkActive : ''}`}
            >
              Photos
            </Link>
            {/* <ul className={styles.subNavWrap}>
              <li className={styles.subNavItem}>
                <Link
                  to='/photos'
                  className={`${styles.menuLink} ${useRouteMatch({
                    path: '/photos*'
                  })
                    ? styles.menuLinkActive
                    : ''}`}
                >
                  Stock Photos
                </Link>
              </li>
              <li className={styles.subNavItem}>
                <a
                  href='https://labs.envato.com/?utm_source=extensions&utm_medium=referral&utm_campaign=wp-elements-nav'
                  target='_blank'
                  rel='noopener noreferrer'
                  className={styles.menuLink}
                >
                  AI Image Gen
                  <span className='dashicons dashicons-external' />
                </a>
              </li>
            </ul> */}
          </li>
        </ul>

        <ul className={`${styles.menuInner} ${styles.menuRight}`}>
          <li className={styles.menuItem}>
            <NavigationConnectButton />
          </li>
          <li className={styles.menuItem}>
            <Link
              to='/settings'
              className={`${styles.menuLink} ${useRouteMatch({ path: '/settings' }) ? styles.menuLinkActive : ''}`}
            >
              <span className='dashicons dashicons-admin-generic' />
            </Link>
          </li>
        </ul>
      </nav>
    </div>
  )
}

export default NavigationBar
