import React from 'react'
import { renderWithGlobalConfig, act, fireEvent } from 'test-utils'
import useGlobalConfig from './useGlobalConfig'

const FreeSubscriptionContainer = () => {
  const { subscriptionStatus, setSubscriptionStatus } = useGlobalConfig()
  return (
    <div>
      <span data-testid='currentSubscriptionStatus'>{subscriptionStatus}</span>
      <button
        data-testid='setSubscriptionPaid' onClick={() => {
          setSubscriptionStatus('paid')
        }}
      >Set Status To Paid
      </button>
    </div>
  )
}

it('should correctly call setGlobalConfig when button is pressed', async () => {
  const { getByTestId } = renderWithGlobalConfig(
    <FreeSubscriptionContainer />,
    {
      config: {
        subscription_status: 'free'
      }
    }
  )
  expect(getByTestId('currentSubscriptionStatus')).toHaveTextContent('free')

  // Perform an action on the mounted component:
  act(() => {
    const setStatusButton = getByTestId('setSubscriptionPaid')
    fireEvent.click(setStatusButton)
  })

  expect(getByTestId('currentSubscriptionStatus')).toHaveTextContent('paid')
})
