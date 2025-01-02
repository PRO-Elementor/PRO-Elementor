import React, { useState } from 'react'
import Filter from './Filter'

export default { title: 'search/Filter' }

const FilterHooked = () => {
  const [dummyState, setDummyState] = useState(null)
  return (
    <>
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
      <pre>Filter: {JSON.stringify(dummyState)}</pre>
    </>
  )
}

export const filter = () => <FilterHooked />

const FilterWithDefaultValueHooked = () => {
  const [dummyState, setDummyState] = useState('Option 3')
  return (
    <>
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
      <pre>Tag: {JSON.stringify(dummyState)}</pre>
    </>
  )
}

export const filterWithDefaultValue = () => <FilterWithDefaultValueHooked />

const FilterColorHooked = () => {
  const [dummyState, setDummyState] = useState(null)
  return (
    <>
      <Filter
        label='Test Color Filter'
        name='colors'
        columns={2}
        attributes={[
          { key: 'Brown' },
          { key: 'Green' },
          { key: 'White' },
          { key: 'Black' },
          { key: 'Blue' },
          { key: 'Red' }
        ]}
        value={dummyState}
        searchFilterChange={({ colors }) => {
          setDummyState(colors)
        }}
      />
      <pre>Color: {JSON.stringify(dummyState)}</pre>
    </>
  )
}

export const colorFilter = () => <FilterColorHooked />
