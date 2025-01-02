import React from 'react'
import { Link } from 'react-router-dom'

const InternalLink = ({ href, text = 'Link' }) => (
  <Link to={href}>{text}</Link>
)

export default InternalLink
