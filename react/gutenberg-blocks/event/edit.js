import {InspectorControls, useBlockProps} from "@wordpress/block-editor";
import {PanelBody, TextControl} from "@wordpress/components";
import {__} from "@wordpress/i18n";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
  const blockProps = useBlockProps();
  const { eventId } = attributes;

  return (
    <>
      <InspectorControls key="setting">
        <PanelBody title={__("Settings", "yoyaku-manager")}>
          <TextControl
            type={"number"}
            label={__("Event ID", "yoyaku-manager")}
            value={eventId}
            onChange={(value) => setAttributes({ eventId: parseInt(value) })}
          />
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>{`[Yoyaku Manager Event ID=[${eventId}]]`}</div>
    </>
  );
}
