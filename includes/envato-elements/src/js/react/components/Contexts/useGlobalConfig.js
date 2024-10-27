import { useContext } from 'react'
import { GlobalConfigContext } from './GlobalConfigProvider'

function useGlobalConfig () {
  const { globalConfig, setConfigValue } = useContext(GlobalConfigContext)

  const appendToGlobalConfig = (key, objectToAppend) => {
    const existingItems = globalConfig[key] || []
    setConfigValue(key, {
      ...existingItems,
      ...objectToAppend
    })
  }

  // Downloaded items config managements:
  const getDownloadedItemId = (humaneId) => {
    return globalConfig.downloaded_items ? globalConfig.downloaded_items[humaneId] : null
  }
  const addDownloadedItem = ({ humaneId, importedId }) => {
    appendToGlobalConfig('downloaded_items', { [humaneId]: importedId })
  }
  const removeDownloadedItem = ({ importedId }) => {
    const downloadedHumanmeIds = Object.keys(globalConfig.downloaded_items)
    downloadedHumanmeIds.forEach(humaneId => {
      if (globalConfig.downloaded_items[humaneId] === importedId) {
        delete (globalConfig.downloaded_items[humaneId])
      }
    })
  }

  // Subscription status config management
  const subscriptionStatus = globalConfig.subscription_status
  const setSubscriptionStatus = (status) => {
    setConfigValue('subscription_status', status)
  }

  // Banner dismissal:
  const bannerHasBeenDismissed = (bannerId) => {
    appendToGlobalConfig('dismissed_banners', { [bannerId]: true })
  }
  const isBannerDismissed = (bannerId) => {
    return globalConfig.dismissed_banners ? globalConfig.dismissed_banners[bannerId] : null
  }

  // Project names
  const getConfigProjectName = () => {
    return globalConfig.project_name
  }
  const setConfigProjectName = (projectName) => {
    setConfigValue('project_name', projectName)
  }

  // Magic button inserting templates
  const getMagicButtonMode = () => {
    return globalConfig.magicButtonMode
  }
  const setMagicButtonMode = (mode) => {
    setConfigValue('magicButtonMode', mode)
  }

  // This is for the token generation url
  const getElementsTokenUrl = () => {
    return globalConfig.elements_token_url
  }

  // API URL and Nonce values
  const getApiUrl = () => {
    return globalConfig.api_url
  }
  const getApiNonce = () => {
    return globalConfig.api_nonce
  }

  // Start Page
  const getStartPage = () => {
    return globalConfig.start_page
  }
  const setStartPage = (startPage) => {
    setConfigValue('start_page', startPage)
  }

  return {
    // downloads:
    getDownloadedItemId,
    addDownloadedItem,
    removeDownloadedItem,
    // subscriptions:
    subscriptionStatus,
    setSubscriptionStatus,
    // banner management:
    bannerHasBeenDismissed,
    isBannerDismissed,
    // project name
    getConfigProjectName,
    setConfigProjectName,
    // magic button modes
    getMagicButtonMode,
    setMagicButtonMode,
    // elements token url helper:
    getElementsTokenUrl,
    // api url and nonce
    getApiUrl,
    getApiNonce,
    // start page
    getStartPage,
    setStartPage
  }
}

export default useGlobalConfig
