import React, { useState } from 'react'
import SearchUi from './SearchUi'
import mockedSearchResults from '../../../../../__mocks__/freeTemplateKits'

export default { title: 'search/Free Template Kits' }

const SearchFormHooked = () => {
  const [dummyState, setDummyState] = useState({
    text: 'Default Text Search'
  })
  const aggregations = { industries: mockedSearchResults.meta.industries }
  return (
    <>
      <SearchUi
        searchParams={dummyState}
        onSearchSubmitted={(args) => {
          setDummyState({ ...dummyState, ...args })
        }}
        aggregations={aggregations}
      />
      <pre>{JSON.stringify(dummyState)}</pre>
    </>
  )
}

export const searchForm = () => <SearchFormHooked />
