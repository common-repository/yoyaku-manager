import { AddBtn } from "@/components/common";
import { Stack } from "react-bootstrap";

export const Header = ({ title, children }) => {
  return (
    <Stack direction="horizontal" gap={1}>
      <h1>{title}</h1>
      {children}
    </Stack>
  );
};

export const HeaderWithAdd = ({ title, to = "/add" }) => {
  return (
    <Header title={title}>
      <AddBtn to={to} />
    </Header>
  );
};

export const H2WithAddBtn = ({ title, to = "/add" }) => {
  return (
    <Stack direction="horizontal" gap={1} className="mb-3">
      <h2>{title}</h2>
      <AddBtn to={to} />
    </Stack>
  );
};
