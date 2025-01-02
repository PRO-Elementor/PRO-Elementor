import React from 'react'
import { HashRouter as Router } from 'react-router-dom'
import mockedSearchResults from '../../../../../__mocks__/premiumTemplateKits'
import SearchResultItem from './SearchResultItem'

export default { title: 'search/Template Kits' }

export const searchResultWithGallery = () => {
  const mockSearchResult = mockedSearchResults.results.search_query_result.search_payload.items[1]
  return (
    <Router>
      <div style={{ width: '500px' }}>
        <SearchResultItem item={mockSearchResult} />
      </div>
    </Router>
  )
}
export const searchResultWithSingleImage = () => {
  const mockSearchResult = mockedSearchResults.results.search_query_result.search_payload.items[0]
  return (
    <Router>
      <div style={{ width: '500px' }}>
        <SearchResultItem item={mockSearchResult} />
      </div>
    </Router>
  )
}
