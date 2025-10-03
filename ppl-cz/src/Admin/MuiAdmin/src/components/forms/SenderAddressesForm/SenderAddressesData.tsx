import SenderAddressesForm from "./SenderAddressesForm";
import Box from "@mui/material/Box";
import Card from "@mui/material/Card";
import Skeleton from "@mui/material/Skeleton";
import Typography from "@mui/material/Typography";
import { useSenderAddressesQuery } from "../../../queries/settings";
import SettingPrintForm from "../SettingPrintForm";

const SenderAddressesData = () => {
  const data = useSenderAddressesQuery();
  const nod = (() => {
    if (data)
      return (
        <>
          <SenderAddressesForm data={data} />
        </>
      );
    else return <Skeleton height={150} sx={{ transform: "scale(1,1)" }} />;
  })();

  return (
    <Card id="etiquete">
      <Box p={2}>
        <Typography variant="h3" marginBottom={4}>
          Etiketa
        </Typography>
        {nod}
      </Box>
      <Box p={2}>
        <Typography variant="h3" marginBottom={4}>
          Tisk
        </Typography>
        <SettingPrintForm />
      </Box>
    </Card>
  );
};

export default SenderAddressesData;
