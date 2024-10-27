import React from 'react'
import ButtonWrapper from '../Buttons/ButtonWrapper'
import ImportFreeBlockButton from '../Buttons/ImportFreeBlockButton'
import ItemCard from '../Card/ItemCard'
import useGlobalConfig from '../Contexts/useGlobalConfig'
import styles from '../Card/KitCard.module.scss'

const SearchResultItem = ({ item }) => {
  // We read the item downloaded value out of our global react context:
  const { getDownloadedItemId } = useGlobalConfig()
  return (
    <ItemCard
      Images={(
        <div className={styles.itemImageLink}>
          <img src={item.preview_image} alt={item.name} className={styles.itemImage} />
        </div>
      )}
      Buttons={(
        <ButtonWrapper>
          <ImportFreeBlockButton
            blockId={item.id}
            jsonUrl={item.import_file}
            importedBlockId={getDownloadedItemId(item.id)}
          />
        </ButtonWrapper>
      )}
      title={item.name}
      description=''
    />
  )
}

export default SearchResultItem
