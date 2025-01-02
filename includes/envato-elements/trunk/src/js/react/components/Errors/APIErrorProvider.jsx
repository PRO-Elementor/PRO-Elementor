import React, { useState, useCallback } from 'react'

export const APIErrorContext = React.createContext({
  errors: [],
  addError: () => {},
  removeError: () => {}
})

export default function APIErrorProvider ({ children }) {
  const [errors, setErrors] = useState([])

  const removeError = (errorToRemove) => {
    setErrors(errors => errors.filter(error => error !== errorToRemove))
  }

  const addError = (code, message, debug) => {
    setErrors(errors => ([...errors, { code, message, debug }]))
  }

  const contextValue = {
    errors,
    addError: useCallback((code, message, debug) => addError(code, message, debug), []),
    removeError: useCallback((error) => removeError(error), [])
  }

  return (
    <APIErrorContext.Provider value={contextValue}>
      {children}
    </APIErrorContext.Provider>
  )
}
