import React, { useState } from 'react'
import TextInput from './TextInput'

export default { title: 'search/Text Input' }

const TextInputHooked = () => {
  const [dummyState, setDummyState] = useState('Cats')
  return (
    <>
      <TextInput
        searchParams={{ text: dummyState }}
        onSearchSubmitted={({ text }) => {
          setDummyState(text)
        }}
      />
      <pre>Submitted Search Text: {dummyState}</pre>
    </>
  )
}

export const textInput = () => <TextInputHooked />
