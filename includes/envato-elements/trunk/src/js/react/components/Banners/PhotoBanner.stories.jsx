import React from 'react'
import PhotoBanner from './PhotoBanner'
import GlobalConfigProvider from '../Contexts/GlobalConfigProvider'

export default { title: 'banners' }

export const photos = () => {
  return (
    <GlobalConfigProvider config={{
      elements_token_url: 'https://api.extensions.envato.com/example-token-url'
    }}
    >
      <PhotoBanner />
    </GlobalConfigProvider>
  )
}
