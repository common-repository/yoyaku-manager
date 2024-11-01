const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require("path");

/**
 * @のエイリアスを解決できるようにwebpack.configを拡張する
 * https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/
 */
module.exports = {
  ...defaultConfig,
  resolve: {
    ...defaultConfig.resolve,
    alias: {
      ...defaultConfig.resolve.alias,
      "@": path.resolve(__dirname, "react"),
    },
  },
};
