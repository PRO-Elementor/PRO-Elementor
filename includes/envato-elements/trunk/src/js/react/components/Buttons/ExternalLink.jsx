import React from 'react'

const ExternalLink = ({ href, text = 'Link' }) => (
  <a href={href} target='_blank' rel='noopener noreferrer'>{text}</a>
)

export default ExternalLink
