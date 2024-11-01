/**
 * 全ページのベース
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */
export const BasePage = (props) => {
  // wrapクラス: contentのmarginをレスポンシブに設定してくれる
  return <div className="wrap yoyaku">{props.children}</div>;
};

/**
 * アクティベートされている時のみ表示するページのベース
 * 設定ページ以外に使われる
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */
export const BaseLicensePage = (props) => {
  return <BasePage {...props} />;
};
