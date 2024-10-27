import React from 'react'
import ButtonWrapper from '../Buttons/ButtonWrapper'
import InstallFreeTemplateKitButton from '../Buttons/InstallFreeTemplateKitButton'
import ItemCard from '../Card/ItemCard'
import ImageCarousel from '../Card/ImageCarousel'
import useGlobalConfig from '../Contexts/useGlobalConfig'
import ExternalLinkButton from '../Buttons/ExternalLinkButton'
import applyImgixToFreeTemplateImageUrls from '../../utils/applyImgixToFreeTemplateImageUrls'

const SearchResultItem = ({ item }) => {
  // We read the item downloaded value out of our global react context:
  const { getDownloadedItemId } = useGlobalConfig()
  if (item.industry.blocks) {
    // If we're displaying a "block" kit, the first thumbnail is always super tiny.
    // The second thumbnail is always nice, so this moves the first block thumbnail to the
    // end of the slideshow list.
    item.thumbnails = [...item.thumbnails.slice(1), item.thumbnails[0]]
  }
  return (
    <ItemCard
      Images={(
        <ImageCarousel
          imageUrls={item.thumbnails.map(thumbnailUrl => applyImgixToFreeTemplateImageUrls(thumbnailUrl, {
            w: 500,
            h: 330,
            q: 90,
            auto: 'format',
            fit: 'crop',
            crop: 'top'
          }))}
        />
      )}
      Buttons={(
        <ButtonWrapper>
          <InstallFreeTemplateKitButton
            templateKitId={item.ID}
            zipUrl={item.zip}
            importedTemplateKitId={getDownloadedItemId(item.ID)}
          />
          <ExternalLinkButton type='secondary' label='Preview' openNewWindow='true' href={`https://wp.envatoextensions.com/preview/?collection=${item.ID}`} />
        </ButtonWrapper>
      )}
      title={item.name}
      description={Object.entries(item.industry).map(entry => (entry[1])).join(', ')}
    />
  )
}

export default SearchResultItem
