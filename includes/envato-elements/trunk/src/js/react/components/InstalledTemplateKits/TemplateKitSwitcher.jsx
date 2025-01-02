import React from 'react'
import fetchInstalledTemplateKits from '../../api/fetchInstalledTemplateKits'
import styles from './TemplateKitSwitcher.module.scss'

const TemplateKitSwitcher = ({ currentKitId, handleChangeKitId }) => {
  const importedKits = fetchInstalledTemplateKits()
  return (
    <>
      {!importedKits.loading && importedKits.data && importedKits.data.length > 1
        ? (
          <div className={styles.currentKit}>
            <div className={styles.optionKitWrapper}>
              <div className={styles.optionKit}>
                <button
                  className={`${styles.optionKitLink} ${currentKitId === 'all' ? styles.optionKitLinkCurrent : ''}`} onClick={() => {
                    handleChangeKitId('all')
                  }}
                >All Kits
                </button>
              </div>
              {importedKits.data.map(item => (
                <div className={styles.optionKit} key={item.id}>
                  <button
                    className={`${styles.optionKitLink} ${currentKitId === item.id ? styles.optionKitLinkCurrent : ''}`} onClick={() => {
                      handleChangeKitId(item.id)
                    }}
                  >
                    {item.title}
                  </button>
                </div>
              ))}
            </div>
          </div>
          )
        : null}
    </>
  )
}

export default TemplateKitSwitcher
