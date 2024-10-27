import React, { useState } from 'react'
import { render, fireEvent, act } from 'test-utils'
import Filter from './Filter'

const FilterHooked = () => {
  const [dummyState, setDummyState] = useState(null)
  return (
    <Filter
      label='Test Filter'
      name='demo'
      attributes={[
        { key: 'Option 1' },
        { key: 'Option 2' },
        { key: 'Option 3' },
        { key: 'Option 4' }
      ]}
      value={dummyState}
      searchFilterChange={({ demo }) => {
        setDummyState(demo)
      }}
    />
  )
}

test('should select correct checkbox on click', () => {
  // We render an instance of <Filter> and get a `getByTestId()` helper back from it
  const { getByTestId } = render(<FilterHooked />)

  // We expect our checkbox to be unselected by default
  expect(getByTestId('filterdemoOption 1')).not.toBeChecked()

  // We wrap any state changing events in act()
  act(() => {
    // We fire off a click event on the first option in the list:
    fireEvent.click(getByTestId('filterdemoOption 1'))
  })

  // We now expect this checkbox to be checked:
  expect(getByTestId('filterdemoOption 1')).toBeChecked()

  // And we expect the second checkbox not to be checked:
  expect(getByTestId('filterdemoOption 2')).not.toBeChecked()

  act(() => {
    // We fire off a click event on the first option in the list:
    fireEvent.click(getByTestId('filterdemoOption 1'))
  })

  // We now expect this checkbox to NOT be checked again after clicking it a second time:
  expect(getByTestId('filterdemoOption 1')).not.toBeChecked()
})
