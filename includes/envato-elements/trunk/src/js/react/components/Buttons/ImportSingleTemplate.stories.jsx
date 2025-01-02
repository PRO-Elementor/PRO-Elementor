import React, { useState } from 'react'
import { MemoryRouter as Router } from 'react-router'
import ImportSingleTemplate from './ImportSingleTemplate'

export default { title: 'import/Template' }

const SuccessfulImportHooked = () => {
  const [errorMessage, setErrorMessage] = useState(null)
  const defaultLoadingState = {
    loading: true,
    data: null,
    error: null
  }
  const successErrorState = {
    loading: false,
    data: null,
    error: false
  }
  const [fakeApiHookState, setFakeApiHookState] = useState(defaultLoadingState)
  return (
    <Router>
      <ImportSingleTemplate
        templateKitId={1} templateId={1} existingImports={[]} customActionHook={() => {
        // After a little while we change to a success state:
          setTimeout(() => { setFakeApiHookState(successErrorState) }, 500)
          // Then reset back to a default state:
          setTimeout(() => { setFakeApiHookState(defaultLoadingState) }, 1000)
          return fakeApiHookState
        }}
        errorCallback={(data) => { setErrorMessage(data.error) }}
      />
      <div>
        {errorMessage}
      </div>
    </Router>
  )
}

export const successfulImport = () => <SuccessfulImportHooked />

const ImportWithErrorHooked = () => {
  const [errorMessage, setErrorMessage] = useState(null)
  const defaultLoadingState = {
    loading: true,
    data: null,
    error: null
  }
  const errorLoadingState = {
    loading: false,
    data: {
      error: 'Failed to find template with ID of 1'
    },
    error: true
  }
  const [fakeApiHookState, setFakeApiHookState] = useState(defaultLoadingState)
  return (
    <Router>
      <ImportSingleTemplate
        templateKitId={1} templateId={1} existingImports={[]} customActionHook={() => {
        // After a little while we change to an error state:
          setTimeout(() => { setFakeApiHookState(errorLoadingState) }, 500)
          // Then reset back to a default state:
          setTimeout(() => { setFakeApiHookState(defaultLoadingState) }, 1000)
          return fakeApiHookState
        }}
        errorCallback={(data) => { setErrorMessage(data.error) }}
      />
      <div>
        {errorMessage}
      </div>
    </Router>
  )
}

export const failsWithError = () => <ImportWithErrorHooked />

export const alreadyImported = () => {
  return (
    <Router>
      <ImportSingleTemplate
        templateKitId={1} templateId={1} existingImports={[
          {
            imported_template_id: 123
          }
        ]}
      />
    </Router>
  )
}
