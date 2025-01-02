import React from 'react'
import { renderWithGlobalConfig, act, fireEvent } from 'test-utils'
import useGlobalConfig from './useGlobalConfig'

/**
 * Renders an individual fake item with knowledge of installed state.
 *
 * @param humaneId
 * @returns {*}
 * @constructor
 */
const ItemTest = ({ humaneId }) => {
  const { getDownloadedItemId, addDownloadedItem } = useGlobalConfig()
  return (
    <div data-testid={`fake-item-${humaneId}`}>
      <h2>
        Item: {humaneId}
      </h2>
      <div>
        {getDownloadedItemId(humaneId)
          ? (
            <span data-testid={`item-has-been-downloaded-${humaneId}`}>Download complete!</span>
            )
          : (
            <button
              data-testid={`download-item-${humaneId}`} onClick={() => {
                addDownloadedItem({ humaneId, importedId: 543 })
              }}
            >Download Item
            </button>
            )}
      </div>
    </div>
  )
}

/**
 * Renders a grid of fake items so we can test clicking buttons on them:
 *
 * @returns {*}
 * @constructor
 */
const ItemTestWrapper = () => {
  const fakeItems = [
    { humaneId: '421GTH' },
    { humaneId: '123HGF' },
    { humaneId: '8SDFJ4' },
    { humaneId: 'YHG432' },
    { humaneId: 'OPY678' }
  ]
  return (
    <div>
      {fakeItems.map(item => (
        <ItemTest key={item.humaneId} humaneId={item.humaneId} />
      ))}
    </div>
  )
}

it('should handle downloaded items state correctly', async () => {
  const { getByTestId } = renderWithGlobalConfig(
    <ItemTestWrapper />,
    {
      config: {
        downloaded_items: {
          '421GTH': 21,
          '123HGF': 4,
          '8SDFJ4': 813
        }
      }
    }
  )

  // We check if one of our already downloaded items is flagged as such:
  expect(getByTestId('item-has-been-downloaded-421GTH')).toBeTruthy()

  // Install one of our fake items, this should update react context:
  act(() => {
    const downloadButton = getByTestId('download-item-OPY678')
    fireEvent.click(downloadButton)
  })

  // Check the component updated and has changed to "Download complete!" text:
  expect(getByTestId('item-has-been-downloaded-OPY678')).toBeTruthy()
})
