import { useContext } from 'react'
import { APIErrorContext } from './APIErrorProvider'

function useAPIError () {
  const { errors, addError, removeError } = useContext(APIErrorContext)
  return { errors, addError, removeError }
}

export default useAPIError
