import { useWorkers } from "@/api";
import { LoadingData } from "@/components/common";
import { HandleError } from "@/pages/others";
import { __ } from "@wordpress/i18n";
import { Form } from "react-bootstrap";

export const SelectOrganizerField = ({ wpId, setValue }) => {
  const { data, error, isLoading } = useWorkers({});

  if (error) return <HandleError error={error} />;
  if (isLoading) return <LoadingData />;

  return (
    <>
      <Form.Group className="form-group">
        <Form.Label>{__("Organizer", "yoyaku-manager")}</Form.Label>
        <Form.Select
          defaultValue={wpId}
          onChange={(e) => {
            if (e.target.value === "") {
              setValue("wp_id", null);
            } else {
              setValue("wp_id", parseInt(e.target.value));
            }
          }}
        >
          <option value={null}></option>
          {data &&
            data.items.map((item) => (
              <option value={item.id}>{item.display_name}</option>
            ))}
        </Form.Select>
      </Form.Group>
    </>
  );
};
