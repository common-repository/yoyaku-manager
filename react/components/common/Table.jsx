/**
 * テーブルの各行のアクションのラッパー
 * コンポーネントを非描画にすると高さが変わるため、visibility: hidden 要素を持つクラスで表示制御する
 * @param children
 * @param isVisible
 * @returns {JSX.Element}
 * @constructor
 */
export const TableActionGroup = ({ children, isVisible }) => {
  const className = isVisible
    ? "table-action-group"
    : "table-action-group table-action-group-hidden";
  return <div className={className}>{children}</div>;
};

export const RecordsInfo = (props) => {
  return <div className="pt-2 pb-1 records-info">{props.children}</div>;
};

export const ActionDivider = () => (
  <span className="text-secondary px-1 align-middle">|</span>
);
