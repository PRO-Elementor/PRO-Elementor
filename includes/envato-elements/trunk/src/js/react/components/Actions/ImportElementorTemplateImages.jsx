import React, { useEffect, useState } from 'react'
import PropTypes from 'prop-types'
import importElementorTemplateImage from '../../api/importElementorTemplateImage'

const ImportImageInBackground = ({ image, templateKitName, completeCallback }) => {
  const { loading, error, data } = importElementorTemplateImage({ ...image, templateKitName })

  useEffect(() => {
    if (!loading && !error && data && data.id) {
      // If we have finished loading (i.e. importing the image finished)
      completeCallback()
    }
  }, [loading])

  return null
}

const extractImagesFromTemplateData = (templateData) => {
  const images = []
  const recusiveIterate = (obj) => {
    if (obj) {
      Object.keys(obj).forEach(key => {
        // Check this node for the existence of a .url and a .id property:
        if (obj[key] && obj[key].url && obj[key].id) {
          images.push(obj[key])
        }

        if (typeof obj[key] === 'object' || Array.isArray(obj[key])) {
          recusiveIterate(obj[key])
        }
      })
    }
  }
  recusiveIterate(templateData.content)
  return images
}

const ImportElementorTemplateImages = ({ templateData, progressCallback, completeCallback }) => {
  const [importImageIndex, setImportImageIndex] = useState(0)
  const [imagesToImport, setImagesToImport] = useState(null)

  useEffect(() => {
    setImagesToImport(extractImagesFromTemplateData(templateData))
  }, [])

  // We call this function when we're ready to import the next image.
  const importNextImage = () => {
    setImportImageIndex(oldStep => oldStep + 1)
  }

  useEffect(() => {
    // Send a percentage progress to the parent component so it can do a UI update etc..
    if (importImageIndex && importImageIndex > 0 && imagesToImport && imagesToImport.length > 0) {
      progressCallback(Math.round((importImageIndex / (imagesToImport.length + 2)) * 100) / 100)
      if (importImageIndex === imagesToImport.length) {
        // we have completed the image import process, call our complete callback so it can continue on with the rest of the import procedure.
        setImportImageIndex(null)
        completeCallback()
      }
    }
  }, [importImageIndex])

  useEffect(() => {
    // Handle case where no images exist in the template data.
    if (imagesToImport !== null && imagesToImport.length === 0) {
      progressCallback(0.5)
      completeCallback()
    }
  }, [imagesToImport])

  if (imagesToImport === null) {
    return null
  }

  // We have some missing requirements, display a banner with a button that opens a modal:
  return (
    <>
      {imagesToImport.map((image, index) => {
        return (
          <React.Fragment key={`importImage${index}`}>
            {importImageIndex === index || importImageIndex > index
              ? (
                <ImportImageInBackground key={`importImageBackground${index}`} image={image} templateKitName={templateData.template_kit_name} completeCallback={importNextImage} />
                )
              : null}
          </React.Fragment>
        )
      })}
    </>
  )
}

ImportElementorTemplateImages.propTypes = {
  templateData: PropTypes.shape(
    {
      author: PropTypes.string,
      file: PropTypes.string
    }
  ).isRequired,
  completeCallback: PropTypes.func.isRequired
}

export default ImportElementorTemplateImages
