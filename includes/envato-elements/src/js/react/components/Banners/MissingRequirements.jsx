import React, { useEffect, useState } from 'react'
import PropTypes from 'prop-types'
import styles from './MissingRequirements.module.scss'
import ModalWrapper from '../Modal/ModalWrapper'
import installRequirement from '../../api/installRequirement'
import MainHeading from '../Titles/MainHeading'
import Button from '../Buttons/Button'
import ExternalLink from '../Buttons/ExternalLink'

const InstallRequirementInBackground = ({ requirement, completeCallback }) => {
  if (!requirement) {
    // If the user has chosen to skip this requirement (by unselecting it)
    // then we fire the complete callback and return a 'Skipped' mesage for the UI to display
    useEffect(() => {
      completeCallback()
    }, [])
    return 'Skipped'
  }
  const { loading, data, error } = installRequirement(requirement)

  useEffect(() => {
    if (!loading) {
      // If we have finished loading (i.e. installing the requirement)
      // we call our completeCallback but we don't care if we errored on this one
      // just continue onto the next.
      completeCallback(error)
    }
  }, [loading])

  return (
    <>
      {loading
        ? (
          <>
            <span className={`dashicons dashicons-update ${styles.installingIcon}`} />
            Installing...
          </>
          )
        : null}
      {error
        ? (
          <>
            <span className='dashicons dashicons-no' />
            {data && data.error
              ? (
                <>
                  {data.error.data && data.error.data.url
                    ? <a href={data.error.data.url} target='_blank' rel='noopener noreferrer'>{data.error.message}</a>
                    : data.error.message}
                </>
                )
              : 'Error'}
          </>
          )
        : null}
      {!loading && !error
        ? (
          <>
            <span className='dashicons dashicons-yes-alt' />
            Success!
          </>
          )
        : null}
    </>
  )
}

const RequiredCSSPreview = ({ previewCss }) => {
  const [openRequiredCSSModal, setOpenRequiredCSSModal] = useState(false)
  return (
    <>
      {openRequiredCSSModal
        ? (
          <ModalWrapper isOpen onCloseCallback={() => setOpenRequiredCSSModal(false)}>
            <code className={styles.cssPreview}>
              <pre>
                {previewCss}
              </pre>
            </code>
          </ModalWrapper>)
        : null}{' '}
      <a
        href='#'
        onClick={(event) => {
          event.preventDefault()
          setOpenRequiredCSSModal(true)
          return false
        }}
      >
        Preview CSS
      </a>
    </>
  )
}

const MissingRequirements = ({ plugins, theme, settings, requiredCss, templateKitId, completeCallback }) => {
  const [openRequirementsModal, setOpenRequirementsModal] = useState(false)
  const [installingIndex, setInstallingIndex] = useState(null)
  const [requirementsToInstall, setRequirementsToInstall] = useState({})

  const installNextRequirement = () => {
    setInstallingIndex(oldStep => oldStep + 1)
  }

  const missingRequirements = []
  /**
  plugins is an array of objects that looks like this:

  plugins = [
    {
      author: "Elementor.com"
      file: "elementor/elementor.php"
      name: "Elementor"
      slug: "elementor"
      status: "activated"
      url: ""
      version: "2.9.6"
    }
  ]
  */
  plugins.forEach(plugin => {
    if (plugin.status !== 'activated') {
      missingRequirements.push({
        plugin
      })
    }
  })

  /**
  settings is an array of objects that looks like this:

  settings = [
    {
      name: "Elementor default color schemes"
      setting_name: "elementor_disable_color_schemes"
    }
  ]
  */
  settings.forEach(setting => {
    missingRequirements.push({
      setting
    })
  })

  /**
  required css is an array of objects that looks like this:

  required_css = [
    {
      name: "Global CSS"
      description: "This is the global CSS for this template kit, this can be edited from the WordPress Customizer."
      file: "css/customizer.css"
    }
  ]
  */
  requiredCss.forEach(requiredCss => {
    missingRequirements.push({
      requiredCss: { ...requiredCss, templateKitId }
    })
  })

  const missingCount = missingRequirements.length

  // If we don't have any missing requirements then we can skip rendering anything for this banner
  if (missingCount === 0) {
    return null
  }

  const isRequirementSelectedForInstall = (index) => {
    return typeof requirementsToInstall[index] === 'undefined' || requirementsToInstall[index]
  }

  // We have some missing requirements, display a banner with a button that opens a modal:
  return (
    <>
      {openRequirementsModal
        ? (
          <ModalWrapper isOpen onCloseCallback={completeCallback}>
            <div>
              <MainHeading title='Missing Requirements' />
              <p className={styles.notice}>Please install and activate these missing requirements for this Template Kit to work correctly. We recommend checking with your web developer before applying these changes.</p>
              <ul className={styles.requirements}>
                {missingRequirements.map((requirement, index) => {
                  return (
                    <li key={`requirement${index}`} className={styles.requirement}>
                      <div className={styles.checkbox}>
                        <input
                          type='checkbox' id={`requirement${index}`} name='installRequirement[]' value='1' disabled={installingIndex !== null} checked={isRequirementSelectedForInstall(index)} onChange={(e) => {
                            const isChecked = !!e.target.checked
                            setRequirementsToInstall(oldRequirements => ({ ...oldRequirements, [index]: isChecked }))
                          }}
                        />
                      </div>
                      <div className={styles.text}>
                        <label htmlFor={`requirement${index}`}>
                          {requirement.theme ? `Theme: ${requirement.theme.name}` : null}
                          {requirement.plugin ? `Plugin: ${requirement.plugin.name}` : null}
                          {requirement.setting ? `Setting: ${requirement.setting.name}` : null}
                          {requirement.requiredCss
                            ? (
                              <>
                                {requirement.requiredCss.name}: {requirement.requiredCss.description}
                                <RequiredCSSPreview previewCss={requirement.requiredCss.css_preview} />
                              </>
                              )
                            : null}
                        </label>
                      </div>
                      <div className={styles.status}>
                        {installingIndex === index || installingIndex > index
                          ? (
                            <InstallRequirementInBackground key={`installRequirement${index}`} requirement={isRequirementSelectedForInstall(index) ? requirement : null} completeCallback={installNextRequirement} />
                            )
                          : null}
                      </div>
                    </li>
                  )
                })}
                {theme && theme.name
                  ? (
                    <li className={styles.requirement}>
                      <div className={styles.checkbox}>
                        <span className='dashicons dashicons-warning' />
                      </div>
                      <div className={styles.text}>
                        FYI: This Template Kit has only been tested with the "{theme.name}" WordPress theme. <br />
                        If the imported templates don’t look correct please read <ExternalLink href='https://help.market.envato.com/hc/en-us/sections/360007560992-Template-Kits' text='this article' />.
                      </div>
                    </li>
                    )
                  : null}
              </ul>
              <div className={styles.footer}>
                {installingIndex === null
                  ? (
                    <Button
                      type='primary' icon='plus' label='Install Above Selected Requirements' onClick={() => {
                        setInstallingIndex(0)
                      }}
                    />
                    )
                  : (
                    <>
                      {installingIndex >= missingCount
                        ? (
                          <>
                            <p className={styles.notice}>Once the above is completed you can close this window.</p>
                            <Button
                              type='primary' icon='plus' label='Close' onClick={completeCallback}
                            />
                          </>
                          )
                        : (
                          <p className={styles.notice}>
                            Installing...
                          </p>
                          )}
                    </>
                    )}
              </div>
            </div>
          </ModalWrapper>
          )
        : null}
      <div className={styles.wrapper}>
        <div className={styles.textWrapper}>
          <strong>Attention!</strong> There are {missingCount} requirements that need installing for this Template Kit to work correctly.
        </div>
        <div className={styles.buttonWrapper}>
          <Button type='attention' label='Install Requirements' icon='info' onClick={() => { setOpenRequirementsModal(true) }} />
        </div>
      </div>
    </>
  )
}

MissingRequirements.propTypes = {
  plugins: PropTypes.arrayOf(PropTypes.shape({
    author: PropTypes.string,
    file: PropTypes.string,
    name: PropTypes.string,
    slug: PropTypes.string,
    status: PropTypes.string,
    url: PropTypes.string,
    version: PropTypes.string
  })),
  settings: PropTypes.arrayOf(PropTypes.shape({
    name: PropTypes.string,
    setting_name: PropTypes.string
  })),
  RequiredCss: PropTypes.arrayOf(PropTypes.shape({
    name: PropTypes.string,
    description: PropTypes.string,
    file: PropTypes.string
  })),
  templateKitId: PropTypes.number.isRequired,
  completeCallback: PropTypes.func.isRequired
}

MissingRequirements.defaultProps = {
  plugins: [],
  settings: [],
  requiredCss: []
}

export default MissingRequirements
