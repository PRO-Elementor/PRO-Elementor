import React from 'react'
import GridWrapper from '../Grid/GridWrapper'
import TemplateItemCardWrapper from './TemplateItemCardWrapper'

const TemplateList = ({ templates }) => {
  return (
    <GridWrapper includeLastItemSpacer>
      {templates.map(template => (
        <TemplateItemCardWrapper key={template.id} template={template} />
      ))}
    </GridWrapper>
  )
}

export default TemplateList
