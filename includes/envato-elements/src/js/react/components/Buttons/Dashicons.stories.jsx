import React from 'react'

export default { title: 'dashicons' }

export const testIcons = () => {
  const icons = [
    'admin-links',
    'arrow-right-alt2',
    'yes',
    'info',
    'visibility',
    'dismiss',
    'update',
    'external',
    'plus-alt',
    'trash',
    'download',
    'editor-expand'

  ]
  return (
    <>
      {icons.map(icon => <div key={icon}>{icon}: <span className={`dashicons dashicons-${icon}`} /></div>)}
    </>
  )
}
