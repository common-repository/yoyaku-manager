import { __ } from "@wordpress/i18n";
import { BackBtn } from "@/components/common";
import { BasePage } from "@/components/layout";

/**
 * 戻るボタンがあるエラーメッセージ表示用コンポーネントのベース
 * @param message
 * @param code
 * @returns {JSX.Element}
 * @constructor
 */
const ErrorBase = ({ message, code }) => {
  return (
    <BasePage>
      <div className="mt-5 text-center">
        <h1>{message}</h1>
        <p>
          {__("Error Code:", "yoyaku-manager")} {code}
        </p>
        <BackBtn />
      </div>
    </BasePage>
  );
};

/**
 *
 * @param error
 * @returns {JSX.Element}
 * @constructor
 */
export const HandleError = ({ error }) => {
  const message = error.message || __("Failed to load.", "yoyaku-manager");
  const code = error?.data?.status || 400;
  return <ErrorBase message={message} code={code} />;
};

/**
 * @returns {JSX.Element}
 * @constructor
 */
export const Forbidden = () => {
  const message = __("You have no permission to view.", "yoyaku-manager");
  return <ErrorBase message={message} code={403} />;
};
