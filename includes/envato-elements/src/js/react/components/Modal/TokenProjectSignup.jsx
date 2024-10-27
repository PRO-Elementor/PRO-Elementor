import React, { useState } from 'react'
import ModalWrapper from './ModalWrapper'
import Step1Welcome from './TokenProjectSignupSteps/Step1Welcome'
import Step2Token from './TokenProjectSignupSteps/Step2Token'
import Step3ProjectName from './TokenProjectSignupSteps/Step3ProjectName'
import Step4Connected from './TokenProjectSignupSteps/Step4Connected'

const TokenProjectSignup = ({ onCloseCallback, showWelcomeMessaging = false }) => {
  // default the wizard to the first step if we want the welcome messaging, otherwise start at step 1:
  const [currentStep, setCurrentStep] = useState(showWelcomeMessaging ? 0 : 1)
  const goToNextStep = () => {
    setCurrentStep(currentStep + 1)
  }
  return (
    <ModalWrapper isOpen onCloseCallback={onCloseCallback}>
      {({ closeModal }) => {
        if (currentStep === 0) {
          return <Step1Welcome goToNextStep={goToNextStep} />
        }
        if (currentStep === 1) {
          return <Step2Token goToNextStep={goToNextStep} />
        }
        if (currentStep === 2) {
          return <Step3ProjectName goToNextStep={goToNextStep} />
        }
        if (currentStep === 3) {
          return <Step4Connected closeModal={closeModal} />
        }
      }}
    </ModalWrapper>
  )
}

export default TokenProjectSignup
