import React, { useState } from 'react'
import SearchUi from './SearchUi'
import mockedSearchResults from '../../../../../__mocks__/premiumPhotos'

export default { title: 'search/Photos' }

const SearchFormHooked = () => {
  const [dummyState, setDummyState] = useState({
    text: 'Default Text Search'
  })
  const aggregations = mockedSearchResults.results.search_query_result.search_payload.aggregations
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
