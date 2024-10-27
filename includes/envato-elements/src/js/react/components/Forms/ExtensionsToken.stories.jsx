import React, { useState } from 'react'
import { MemoryRouter as Router } from 'react-router'
import ExtensionsToken from './ExtensionsToken'
import GlobalConfigProvider from '../Contexts/GlobalConfigProvider'

export default { title: 'token/Verify' }

const SucessfulPaidToken = () => {
  const defaultLoadingState = {
    loading: true,
    data: null,
    error: null
  }
  const successLoadingState = {
    loading: false,
    data: {
      valid: true,
      token: '12345',
      status: 'paid'
    },
    error: null
  }
  const [fakeApiHookState, setFakeApiHookState] = useState(defaultLoadingState)
  return (
    <GlobalConfigProvider config={{
      elements_token_url: 'https://api.extensions.envato.com/example-token-url'
    }}
    >
      <Router>
        <ExtensionsToken customActionHook={() => {
        // After a little while we change to success state:
          setTimeout(() => { setFakeApiHookState(successLoadingState) }, 500)
          return fakeApiHookState
        }}
        />
      </Router>
    </GlobalConfigProvider>
  )
}

export const successfulPaidToken = () => <SucessfulPaidToken />

const FailedFreeToken = () => {
  const defaultLoadingState = {
    loading: true,
    data: null,
    error: null
  }
  const freeLoadingState = {
    loading: false,
    data: {
      error: {
        code: 'no_paid_account'
      }
    },
    error: true
  }
  const [fakeApiHookState, setFakeApiHookState] = useState(defaultLoadingState)
  return (
    <GlobalConfigProvider config={{
      elements_token_url: 'https://api.extensions.envato.com/example-token-url'
    }}
    >
      <Router>
        <ExtensionsToken customActionHook={() => {
        // After a little while we change to success state:
          setTimeout(() => { setFakeApiHookState(freeLoadingState) }, 500)
          return fakeApiHookState
        }}
        />
      </Router>
    </GlobalConfigProvider>
  )
}

export const failedFreeToken = () => <FailedFreeToken />

const InvalidToken = () => {
  const defaultLoadingState = {
    loading: true,
    data: null,
    error: null
  }
  const failureLoadingState = {
    loading: false,
    data: {
      error: {
        code: 'invalid_token'
      }
    },
    error: true
  }
  const [fakeApiHookState, setFakeApiHookState] = useState(defaultLoadingState)
  return (
    <GlobalConfigProvider config={{
      elements_token_url: 'https://api.extensions.envato.com/example-token-url'
    }}
    >
      <Router>
        <ExtensionsToken customActionHook={() => {
        // After a little while we change to success state:
          setTimeout(() => { setFakeApiHookState(failureLoadingState) }, 500)
          return fakeApiHookState
        }}
        />
      </Router>
    </GlobalConfigProvider>
  )
}

export const invalidToken = () => <InvalidToken />
