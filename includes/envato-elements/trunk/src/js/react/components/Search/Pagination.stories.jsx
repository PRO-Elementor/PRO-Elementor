import React from 'react'
import Pagination from './Pagination'
import mockedSearchResults from '../../../../../__mocks__/premiumTemplateKits'

export default { title: 'search/Pagination' }

export const withFivePages = () => (
  <Pagination
    currentPage={mockedSearchResults.results.current_page}
    totalHits={mockedSearchResults.results.search_query_result.search_payload.total_hits}
    perPage={mockedSearchResults.results.per_page}
    searchParams={{}}
    onSearchSubmitted={() => {}}
  />
)
