import React from 'react'
import Modal from 'react-modal'
import ModalEnvatoIcon from './ModalEnvatoIcon'
import { Link, useRouteMatch } from 'react-router-dom'
import styles from './MagicModalWrapper.module.scss'
const customStyles = {
  overlay: {
    backgroundColor: 'rgba(32, 32, 32, 0.81)',
    zIndex: 199999,
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center'
  },
  content: {
    background: '#f1f1f1',
    border: '0',
    padding: '0',
    right: 'auto',
    bottom: 'auto',
    top: 'auto',
    left: 'auto',
    borderRadius: '4px'
  }
}

const MagicModalWrapper = ({ photoId, photoTitle, onCloseCallback = null, children }) => {
  const [modalIsOpen, setModalIsOpen] = React.useState(true)
  const closeModal = () => {
    setModalIsOpen(false)
    if (onCloseCallback) {
      onCloseCallback()
    }
  }

  // Make sure to bind modal to your appElement (http://reactcommunity.org/react-modal/accessibility/)
  // We get window.envatoElements.modalAppHolder from our initial render in main.jsx:
  if (typeof window !== 'undefined' && window.envatoElements && window.envatoElements.modalAppHolder) {
    Modal.setAppElement(window.envatoElements.modalAppHolder)
  }

  return (
    <Modal
      isOpen={modalIsOpen}
      onRequestClose={closeModal}
      style={customStyles}
      contentLabel='Envato Elements'
      data-testid='modal-wrapper'
    >
      <div className={styles.modalInner}>
        <div className={styles.modalHeader}>
          <div className={styles.modalLogo}>
            <Link to='admin.php?page=envato-elements'>
              <ModalEnvatoIcon />
            </Link>
          </div>
          <div className={styles.headerNav}>
            <Link
              to='/template-kits/installed-kits'
              className={`${styles.menuLink} ${useRouteMatch({ path: '/template-kits/installed-kits' }) ? styles.menuLinkActive : ''}`}
            >
              Installed Kits
            </Link>
            <Link
              to='/template-kits/premium-kits'
              className={`${styles.menuLink} ${useRouteMatch({ path: '/template-kits/premium-kits' }) ? styles.menuLinkActive : ''}`}
            >
              Premium Kits
            </Link>
            <Link
              to='/template-kits/free-kits'
              className={`${styles.menuLink} ${useRouteMatch({ path: '/template-kits/free-kits' }) ? styles.menuLinkActive : ''}`}
            >
              Free Kits
            </Link>
            <Link
              to='/template-kits/free-blocks'
              className={`${styles.menuLink} ${useRouteMatch({ path: '/template-kits/free-blocks' }) ? styles.menuLinkActive : ''}`}
            >
              Free Blocks
            </Link>
          </div>
          <div className={styles.headerActions}>
            <button onClick={closeModal} data-testid='modal-close-button' className={styles.closeButton}>
              <span className={`dashicons dashicons-no-alt ${styles.dismissIcon}`} />
            </button>
          </div>
        </div>
        <div className={styles.magicInner}>
          {typeof children === 'function' ? children({ closeModal }) : children}
        </div>
      </div>
    </Modal>
  )
}

export default MagicModalWrapper
