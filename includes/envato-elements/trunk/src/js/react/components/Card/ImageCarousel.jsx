import React from 'react'
import styles from './ImageCarousel.module.scss'
import Slider from 'react-slick'

import 'slick-carousel/slick/slick.css'
import 'slick-carousel/slick/slick-theme.css'

const NextArrow = ({ onClick }) => (
  <button className={styles.nextArrow} onClick={onClick} type='button' aria-label='Next item'>
    <div className={styles.nextArrowButton}>
      <svg viewBox='0 0 16 31' className={styles.nextArrowIcon}>
        <path d='M.188 27.588l2.232 2.474L15.813 15.22 2.42.375.188 2.849l11.16 12.37-11.16 12.37z' />
      </svg>
    </div>
  </button>
)

const PrevArrow = ({ onClick }) => (
  <button className={styles.prevArrow} onClick={onClick} type='button' aria-label='Previous item'>
    <div className={styles.prevArrowButton}>
      <svg viewBox='0 0 16 31' className={styles.prevArrowIcon}>
        <path d='M15.813 2.849L13.58.375.187 15.219 13.58 30.063l2.232-2.474-11.16-12.37 11.16-12.37z' />
      </svg>
    </div>
  </button>
)

const sliderSettings = {
  dots: false,
  lazyLoad: true,
  infinite: true,
  speed: 500,
  slidesToShow: 1,
  slidesToScroll: 1,
  nextArrow: <NextArrow />,
  prevArrow: <PrevArrow />
}

const ImageCarousel = ({ coverImageUrls = null, imageUrls = [], galleryImageUrls = [] }) => {
  const imageGallery = []
  if (coverImageUrls) {
    imageGallery.push(coverImageUrls.w900 || coverImageUrls.w632)
  }
  if (imageUrls.length) {
    imageGallery.push(...imageUrls)
  }

  if (galleryImageUrls.length) {
    galleryImageUrls.forEach(image => {
      imageGallery.push(image.w900 || image.w632)
    })
  }

  return (
    <>
      <Slider {...sliderSettings}>
        {imageGallery.map((image, index) => (
          <div key={image} className={styles.slide}>
            <img key={image} className={styles.image} src={image} />
          </div>
        ))}
      </Slider>
    </>
  )
}

export default ImageCarousel
