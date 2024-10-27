import React from 'react'
import ButtonWrapper from '../Buttons/ButtonWrapper'
import InstallPremiumTemplateKitButton from '../Buttons/InstallPremiumTemplateKitButton'
import ExternalLinkButton from '../Buttons/ExternalLinkButton'
import ItemCard from '../Card/ItemCard'
import ImageCarousel from '../Card/ImageCarousel'
import useGlobalConfig from '../Contexts/useGlobalConfig'

const SearchResultItem = ({ item }) => {
  // We read the item downloaded value out of our global react context:
  const { getDownloadedItemId } = useGlobalConfig()
  return (
    <ItemCard
      Images={<ImageCarousel coverImage={item.cover_image} galleryImages={item.preview_images} />}
      Buttons={(
        <ButtonWrapper>
          <InstallPremiumTemplateKitButton
            templateKitId={item.humane_id}
            importedTemplateKitId={getDownloadedItemId(item.humane_id)}
          />
          <ExternalLinkButton type='secondary' label='More Info' openNewWindow='true' href={`https://elements.envato.com/${item.slug}-${item.humane_id}?utm_source=extensions&utm_medium=referral&utm_campaign=template_kit_more_info`} />
          <ExternalLinkButton type='secondary' label='Preview' openNewWindow='true' href={item.item_attributes.demo_url} />
        </ButtonWrapper>
      )}
      title={item.title}
      description={`Author: ${item.contributor_username}`}
    />
  )
}

export default SearchResultItem
