import React from 'react'
import useGlobalConfig from '../Contexts/useGlobalConfig'
import {
  Redirect
} from 'react-router-dom'

const FirstPageRedirector = () => {
  const { getStartPage } = useGlobalConfig()

  const possibleStartPageRoutes = {
    welcome: '/welcome',
    'premium-kits': '/template-kits/premium-kits',
    'free-kits': '/template-kits/free-kits',
    'installed-kits': '/template-kits/installed-kits',
    photos: '/photos'
  }

  const preferredStartPage = getStartPage()
  if (possibleStartPageRoutes[preferredStartPage]) {
    return <Redirect to={possibleStartPageRoutes[preferredStartPage]} />
  }

  // Default to the '/welcome' route if none is set in global config
  return <Redirect to='/welcome' />
}

export default FirstPageRedirector
