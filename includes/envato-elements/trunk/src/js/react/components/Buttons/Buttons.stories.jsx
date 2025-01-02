import React from 'react'
import { MemoryRouter as Router } from 'react-router'
import Button from './Button'
import InternalLinkButton from './InternalLinkButton'
import ExternalLinkButton from './ExternalLinkButton'
import ButtonElement from './ButtonElement'
import ButtonIconAndLabel from './ButtonIconAndLabel'
import ExternalLink from './ExternalLink'
import InternalLink from './InternalLink'

export default { title: 'buttons' }

export const primaryButton = () => {
  return (
    <Button type='primary' label='Primary' icon='arrow' />
  )
}

export const ghostyButton = () => {
  return (
    <Button type='ghost' label='Ghost' icon='eye' />
  )
}

export const secondaryButton = () => {
  return (
    <Button type='secondary' label='Secondary' icon='tick' />
  )
}

export const warningButton = () => {
  return (
    <Button type='warning' label='Warning' icon='cross' />
  )
}

export const attentionButton = () => {
  return (
    <Button type='attention' label='Attention' icon='info' />
  )
}

export const disabledButton = () => {
  return (
    <Button disabled label="Oh no you can't click me!" icon='cross' />
  )
}

export const ghostyButtonWithLabelOnly = () => {
  return (
    <Button type='ghost' label='I have no icon' />
  )
}

export const ghostyButtonWithIconOnly = () => {
  return (
    <Button type='ghost' icon='eye' />
  )
}

export const internalLinkButton = () => {
  return (
    <Router>
      <InternalLinkButton href='/some/page' type='secondary' label='Check it out' icon='plus' />
    </Router>
  )
}

export const externalLinkButton = () => {
  return (
    <ExternalLinkButton href='https://mixkit.co/' type='ghost' label='Very cool website' icon='link' openNewWindow />
  )
}

export const buttonIconAndLabel = () => {
  return (
    <ButtonIconAndLabel icon='trash' label='Garbage' />
  )
}

export const buttonElement = () => {
  return (
    <ButtonElement element='button' type='warning' onClick={() => { alert('ow!') }}>
      Stuff goes here
    </ButtonElement>
  )
}

export const externalLink = () => {
  return (
    <Router>
      <ExternalLink text='Open Demo' href='https://envato.com' />
    </Router>
  )
}

export const internalLink = () => {
  return (
    <Router>
      <InternalLink text='View Item' href='/' />
    </Router>
  )
}
