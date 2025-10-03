import { QueryClient, QueryClientProvider } from "@tanstack/react-query";

let qc: QueryClient | null = null;

export const getQueryClient = (): QueryClient => {
  if (!qc) qc = new QueryClient();
  return qc;
};

const QueryContextOverlay = (props: { children: React.ReactNode }) => (
  <QueryClientProvider client={getQueryClient()}>{props.children}</QueryClientProvider>
);
export default QueryContextOverlay;
