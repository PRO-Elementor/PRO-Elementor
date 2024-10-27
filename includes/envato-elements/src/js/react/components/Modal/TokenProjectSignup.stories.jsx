import React from 'react'
import Step1Welcome from './TokenProjectSignupSteps/Step1Welcome'
import Step2Token from './TokenProjectSignupSteps/Step2Token'
import Step3ProjectName from './TokenProjectSignupSteps/Step3ProjectName'
import Step4Connected from './TokenProjectSignupSteps/Step4Connected'
import GlobalConfigProvider from '../Contexts/GlobalConfigProvider'

export default { title: 'token/wizard' }

const FakeModalWrapper = ({ children }) => (
  <div style={{ padding: '40px', backgroundColor: '#fff', borderRadius: '10px', width: '730px', margin: '10px 20px 30px' }}>
    {children}
  </div>
)

export const wizardSteps = () => (
  <GlobalConfigProvider config={{
    elements_token_url: 'https://api.extensions.envato.com/example-token-url'
  }}
  >
    <h1>
      Step 1:
    </h1>
    <FakeModalWrapper>
      <Step1Welcome />
    </FakeModalWrapper>
    <h1>
      Step 2:
    </h1>
    <FakeModalWrapper>
      <Step2Token />
    </FakeModalWrapper>
    <h1>
      Step 3:
    </h1>
    <FakeModalWrapper>
      <Step3ProjectName />
    </FakeModalWrapper>
    <h1>
      Step 4:
    </h1>
    <FakeModalWrapper>
      <Step4Connected />
    </FakeModalWrapper>
  </GlobalConfigProvider>
)
