/**
 * ブロックの動作を定義する
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { calendarIcon } from "@/gutenberg-blocks/components/Icon";
import { registerBlockType } from "@wordpress/blocks";
import metadata from "./block.json";
import Edit from "./edit";
import "./style.scss";

registerBlockType(metadata.name, {
  icon: calendarIcon,
  edit: Edit,
  save: () => null,
});
