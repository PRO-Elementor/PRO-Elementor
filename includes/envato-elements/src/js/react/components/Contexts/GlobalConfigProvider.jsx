import React, { useState, useCallback } from 'react'

export const GlobalConfigContext = React.createContext({
  globalConfig: {},
  setConfigValue: () => {}
})

export default function GlobalConfigProvider ({ children, config = {} }) {
  // We default our configuration object to the WordPress global config variable values:
  const [globalConfig, setGlobalConfig] = useState(Object.keys(config).length > 0 ? config : window.envato_elements)

  // This function updates a particular key within the object:
  const setConfigValue = (key, value) => {
    setGlobalConfig(globalConfig => ({
      ...globalConfig,
      [key]: value
    }))
    // We also update the global window settings object so our magic button works successfully between reloads.
    // Without this when in the Elementor magic button it doesn't remember which templates we have installed between modal loads.
    if (window.envato_elements) {
      window.envato_elements[key] = value
    }
  }

  const contextValue = {
    globalConfig,
    setConfigValue: useCallback((key, value) => setConfigValue(key, value), [])
  }

  return (
    <GlobalConfigContext.Provider value={contextValue}>
      {children}
    </GlobalConfigContext.Provider>
  )
}
