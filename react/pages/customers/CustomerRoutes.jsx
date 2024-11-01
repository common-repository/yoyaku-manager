import { App } from "@/components/layout";
import { Route, Routes } from "react-router-dom";
import { AddCustomer } from "./AddCustomer";
import { CustomerDetail } from "./CustomerDetail";
import { Customers } from "./Customers";
import { UpdateCustomer } from "./UpdateCustomer";

export default () => (
  <App>
    <Routes>
      <Route path="/" element={<Customers />} />
      <Route path="/add" element={<AddCustomer />} />
      <Route path="/:id" element={<CustomerDetail />} />
      <Route path="/:id/update" element={<UpdateCustomer />} />
    </Routes>
  </App>
);
