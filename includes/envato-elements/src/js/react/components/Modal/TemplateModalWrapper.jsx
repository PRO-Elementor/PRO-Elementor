import React, { useEffect } from 'react'
import Modal from 'react-modal'
import ImportSingleTemplate from '../Buttons/ImportSingleTemplate'
import ModalEnvatoIcon from './ModalEnvatoIcon'
import styles from './TemplateModalWrapper.module.scss'

const customStyles = {
  overlay: {
    backgroundColor: 'rgba(32, 32, 32, 0.81)',
    zIndex: 199999
  },
  content: {
    background: '#f1f1f1',
    border: '0',
    top: '50%',
    left: '50%',
    right: 'auto',
    bottom: 'auto',
    marginRight: '-50%',
    padding: '0',
    transform: 'translate(-50%, -50%)',
    borderRadius: '4px'
  }
}

const TemplateModalWrapper = ({ isOpen, onCloseCallback = null, children, templatePreviewTitle, templateId, templateKitId, existingImports }) => {
  const [modalIsOpen, setModalIsOpen] = React.useState(false)
  const closeModal = () => {
    setModalIsOpen(false)
    if (onCloseCallback) {
      onCloseCallback()
    }
  }
  useEffect(() => {
    // If our `isOpen` prop changes we set our local open state value respectively.
    // This allows the user to dismiss our modal by only modifying local state.
    if (isOpen) {
      setModalIsOpen(true)
    }
  }, [isOpen])

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
            <ModalEnvatoIcon />
          </div>
          <div className={styles.headerTitle}>
            {templatePreviewTitle}
          </div>
          <div className={styles.headerActions}>
            <ImportSingleTemplate
              templateKitId={templateKitId}
              templateId={templateId}
              existingImports={existingImports}
            />
            <button onClick={closeModal} data-testid='modal-close-button' className={styles.closeButton}>
              <span className={`dashicons dashicons-no-alt ${styles.dismissIcon}`} />
            </button>
          </div>
        </div>
        <div className={styles.kitInner}>
          {typeof children === 'function' ? children({ closeModal }) : children}
        </div>
      </div>
    </Modal>
  )
}

export default TemplateModalWrapper
