import React from 'react'
import styles from './GridItem.module.scss'

const GridItem = ({ colWidthPercentage = 20, children, className, ...rest }) => {
  const gridItemClasses = [styles.item]
  if (colWidthPercentage === 20) {
    gridItemClasses.push(styles.widthTwenty)
  }
  if (colWidthPercentage === 25) {
    gridItemClasses.push(styles.widthTwentyFive)
  }
  if (colWidthPercentage === 33) {
    gridItemClasses.push(styles.widthThirtyThree)
  }
  if (colWidthPercentage === 40) {
    gridItemClasses.push(styles.widthFourty)
  }
  if (colWidthPercentage === 50) {
    gridItemClasses.push(styles.widthFifty)
  }
  if (colWidthPercentage === 60) {
    gridItemClasses.push(styles.widthSixty)
  }
  if (colWidthPercentage === 100) {
    gridItemClasses.push(styles.widthFull)
  }
  if (className) {
    gridItemClasses.push(className)
  }
  return (
    <div className={gridItemClasses.join(' ')} {...rest}>
      {children}
    </div>
  )
}

export default GridItem
