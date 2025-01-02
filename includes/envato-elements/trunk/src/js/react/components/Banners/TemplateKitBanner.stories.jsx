import React from 'react'
import TemplateKitBanner from './TemplateKitBanner'
import GlobalConfigProvider from '../Contexts/GlobalConfigProvider'

export default { title: 'banners' }

export const templateKit = () => {
  return (
    <GlobalConfigProvider config={{
      elements_token_url: 'https://api.extensions.envato.com/example-token-url'
    }}
    >
      <TemplateKitBanner />
    </GlobalConfigProvider>
  )
}
