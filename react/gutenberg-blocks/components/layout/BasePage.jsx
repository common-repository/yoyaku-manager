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
