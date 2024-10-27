import React from 'react'

/**
 * Link out to a page within WordPress outside of our react app (i.e. to an imported template)
 *
 * @param href
 * @param text
 * @returns {*}
 * @constructor
 */
const WordPressLink = ({ href, text = 'Link' }) => (
  <a href={href}>{text}</a>
)

export default WordPressLink
