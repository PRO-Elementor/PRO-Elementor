import React from 'react'
import { renderWithAPIError, act, fireEvent } from 'test-utils'
import APIErrorMessage from './APIErrorMessage'

it('should remove error when handleSubmit is pressed', async () => {
  const { getByTestId, removeErrorSpy } = renderWithAPIError(
    <APIErrorMessage />,
    {
      errors: [
        {
          code: 'invalid_subscription',
          message: 'Test error message'
        }
      ]
    }
  )

  act(() => {
    const closeModalButton = getByTestId('modal-close-button')
    fireEvent.click(closeModalButton)
  })

  expect(removeErrorSpy).toHaveBeenCalled()
})
