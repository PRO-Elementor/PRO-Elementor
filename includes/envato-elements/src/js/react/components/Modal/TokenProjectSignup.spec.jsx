/* eslint-env jest */
/* eslint no-import-assign: 0 */
import React from 'react'
import { renderWithGlobalConfig, act, fireEvent, waitFor } from 'test-utils'
import TokenProjectSignup from './TokenProjectSignup'
import * as mockVerifyExtensionsToken from '../../api/verifyExtensionsToken'
import * as mockSetProjectName from '../../api/setProjectName'

it('should close modal after completing sign up process', async () => {
  mockVerifyExtensionsToken.default = jest.fn().mockImplementation(() => {
    return {
      loading: false,
      error: null,
      data: { success: true }
    }
  })
  mockSetProjectName.default = jest.fn().mockImplementation(() => {
    return {
      loading: false,
      error: null,
      data: { success: true }
    }
  })
  const onCloseCallback = jest.fn()

  const { getByTestId } = renderWithGlobalConfig((
    <>
      <div id='modalHolder' />
      <TokenProjectSignup onCloseCallback={onCloseCallback} />
    </>),
  {
    config: {
      project_name: 'Default Project Name'
    }
  })

  // Fill in and submit step 1 of the token flow::
  // await act(async () => {
  // Input box:
  const tokenInputBox = getByTestId('token-input')
  fireEvent.change(tokenInputBox, { target: { value: 'test-token-value' } })
  expect(tokenInputBox.value).toBe('test-token-value')

  // Button click
  const tokenSubmitButton = getByTestId('elements-token-submit')
  fireEvent.click(tokenSubmitButton)

  // Check the API call was fired:
  expect(mockVerifyExtensionsToken.default).toHaveBeenCalled()

  // Wait for the modal to swap to step 2
  await waitFor(() => {
    expect(getByTestId('project-name-input')).toBeInTheDocument()
  }, {
    timeout: 2000
  })
  // })

  // Fill in and submit step 2 of the token flow::
  // await act(async () => {
  // Input box:
  const projectInputBox = getByTestId('project-name-input')
  expect(projectInputBox.value).toBe('Default Project Name')
  fireEvent.change(projectInputBox, { target: { value: 'My project name' } })
  expect(projectInputBox.value).toBe('My project name')

  // Button click
  const projectSubmitButton = getByTestId('project-name-submit')
  fireEvent.click(projectSubmitButton)

  // Check the API call was fired:
  expect(mockSetProjectName.default).toHaveBeenCalled()

  // Wait for the modal to swap to final step
  await waitFor(() => {
    expect(getByTestId('complete-signup-wizard')).toBeInTheDocument()
  }, {
    timeout: 2000
  })
  // })

  // Click the final "complete" button
  // await act(async () => {
  // Input box:
  const completeButton = getByTestId('complete-signup-wizard')
  fireEvent.click(completeButton)

  // Wait for modal to be closed
  await waitFor(() => {
    act(() => {
      expect(onCloseCallback).toHaveBeenCalled()
    })
  }, {
    timeout: 4000
  })
  // })
})
