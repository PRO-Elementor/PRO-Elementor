import React, { useState, useEffect } from 'react'

const ActionPerformer = ({ actionHook, LoadingButton, SuccessButton, ErrorButton, errorCallback, completeCallback }) => {
  // Call our useFetch() hook to start performing our action (i.e. importing a template)
  const { loading, data, error } = actionHook()

  // Check if we've successfully completed out action:
  if (!loading && !error) {
    // We've successfully imported the template, bubble up a success call after a short timeout
    // so we can show an "imported" state on the button briefly:
    setTimeout(() => { completeCallback(data) }, 500)
    return SuccessButton
  }

  // Check if we had an error performing the action:
  if (error) {
    setTimeout(() => { errorCallback(data) }, 100)
    return ErrorButton
  }

  // The default state of this component is loading, so we show that loading button here..
  return LoadingButton
}

/**
 * Generic user action provider.
 * Used for things like "Import Template" and "Import Photo" buttons
 *
 * @param DefaultButton
 * @param CompletedButton
 * @param ProcessingButton
 * @param isAlreadyCompleted
 * @param completedCallback
 * @param actionConfirmationMessage
 *
 * @returns {*}
 * @constructor
 */
const ButtonActionProvider = ({ DefaultButton, CompletedButton, LoadingButton, ErrorButton, SuccessButton, actionHook, isAlreadyCompleted = false, completedCallback = null, errorCallback = null, actionConfirmationMessage = null }) => {
  const [isProcessing, setIsProcessing] = useState(false)
  const [isCompleted, setIsCompleted] = useState(isAlreadyCompleted)
  const [error, setError] = useState(null)

  useEffect(() => {
    if (isCompleted && completedCallback && !isAlreadyCompleted) {
      // We fire off an optional completed callback, if the component didn't start in the completed state.
      completedCallback(isCompleted)
    }
  }, [isCompleted])

  useEffect(() => {
    if (error && errorCallback) {
      // We fire off an optional error callback, with the error data set below
      errorCallback(error)
    }
  }, [error])

  useEffect(() => {
    // If our parent component resets our completed status prop, we update our local state to reflect this:
    setIsCompleted(isAlreadyCompleted)
  }, [isAlreadyCompleted])

  // Check if this action has been completed:
  if (isCompleted) {
    return CompletedButton
  }

  // Check if we're currently performing the action:
  if (isProcessing) {
    // As soon as this "Processing" component renders we expect the ajax action to start running.
    // We provide a "completeCallback" that this "Processing" component can call once it's done it's thing.
    return (
      <ActionPerformer
        actionHook={actionHook}
        LoadingButton={LoadingButton}
        ErrorButton={ErrorButton}
        SuccessButton={SuccessButton}
        errorCallback={(data) => {
          // If our action returns an error, we swap back to our default state so the user can try again
          setTimeout(() => { setIsProcessing(false) }, 500)
          setError(data)
        }}
        completeCallback={(data) => {
          setIsProcessing(false)
          setIsCompleted(data)
        }}
      />
    )
  }

  // Default state is not processing/completed, so we show a default button
  return React.cloneElement(DefaultButton, {
    onClick: (event) => {
      if (actionConfirmationMessage) {
        if (!confirm(actionConfirmationMessage)) {
        // user has denied the confirmation dialog, prevent calling our processing action.
          event.preventDefault()
          return false
        }
      }
      setIsProcessing(true)
    }
  })
}

export default ButtonActionProvider
