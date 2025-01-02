import React from 'react'
import { render, fireEvent, act } from 'test-utils'
import TextInput from './TextInput'

test('should set correct text', () => {
  // We render an instance of <TextInput> and get a `getByDisplayValue()` helper back from it
  const { getByDisplayValue } = render(
    <TextInput
      searchParams={{ text: 'Default Text Value' }}
      onSearchSubmitted={() => {}}
    />
  )

  // We expect our text box to exist:
  expect(getByDisplayValue('Default Text Value')).toBeTruthy()

  // We wrap any state changing events in act()
  act(() => {
    // We fire off a text change event on the input box
    fireEvent.change(getByDisplayValue('Default Text Value'), { target: { value: 'Text Changed' } })
  })

  // We expect our changed text box to exist:
  expect(getByDisplayValue('Text Changed')).toBeTruthy()
})
