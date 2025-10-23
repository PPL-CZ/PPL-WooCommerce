import Alert from "@mui/material/Alert";
import Box from "@mui/material/Box";
import Step from "@mui/material/Step";
import StepLabel from "@mui/material/StepLabel";
import Stepper from "@mui/material/Stepper";

interface LabelCreationStepperProps {
  step: number;
  errorMessage: string;
  message: string;
  printUrl: URL | null;
}

export const LabelCreationStepper = ({ step, errorMessage, message, printUrl }: LabelCreationStepperProps) => {
  if (printUrl) return null;

  return (
    <Box p={2}>
      {step >= -1 ? (
        <Box p={2}>
          <Stepper activeStep={step}>
            <Step key={1}>
              <StepLabel>Validace</StepLabel>
            </Step>
            <Step key={2}>
              <StepLabel>Vytvoření zásilek</StepLabel>
            </Step>
            <Step key={3}>
              <StepLabel>Příprava etiket</StepLabel>
            </Step>
          </Stepper>
        </Box>
      ) : null}
      {errorMessage ? <Alert severity="warning">{errorMessage}</Alert> : null}
      {message ? <Alert severity="success">{message}</Alert> : null}
    </Box>
  );
};
