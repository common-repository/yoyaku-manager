import {
  CaretDownFill,
  CaretUpFill,
  CheckCircleFill,
  DashCircleFill,
} from "react-bootstrap-icons";

export const SentStatusIcon = ({ status }) => {
  const className = "sent-status-icon";
  const size = 18;
  if (status) {
    return (
      <CheckCircleFill className={className} size={size} color="#228B22" />
    );
  } else {
    return <DashCircleFill className={className} size={size} color="#DC143C" />;
  }
};

export const SortIconWithNone = ({ order }) => {
  const size = 20;
  const className = "sort-icon";
  if (order === "asc") {
    return <CaretUpFill className={className} size={size} />;
  } else if (order === "desc") {
    return <CaretDownFill className={className} size={size} />;
  } else {
    return <span className="ps-2">-</span>;
  }
};

export const SortIcon = ({ order }) => {
  if (order === "asc" || order === "desc") {
    return <SortIconWithNone order={order} />;
  } else {
    return null;
  }
};
