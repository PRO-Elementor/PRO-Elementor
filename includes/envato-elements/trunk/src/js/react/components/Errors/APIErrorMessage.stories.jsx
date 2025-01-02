import React from 'react'
import { MemoryRouter as Router } from 'react-router'
import useAPIError from './useAPIError'
import APIErrorProvider from './APIErrorProvider'
import APIErrorMessage from './APIErrorMessage'

export default { title: 'error/Global API Errors' }

const TestAPIErrorWrapper = ({ children }) => {
  return (
    <APIErrorProvider>
      <Router>
        <APIErrorMessage />
        {children}
      </Router>
    </APIErrorProvider>
  )
}

const SubscriptionError = () => {
  const { addError } = useAPIError()
  return (
    <button onClick={() => { addError('invalid_subscription', 'Error Message Here') }}>Trigger "invalid_subscription"</button>
  )
}

export const subscriptionError = () => (
  <TestAPIErrorWrapper>
    <SubscriptionError />
  </TestAPIErrorWrapper>
)
