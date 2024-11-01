import {Form} from "react-bootstrap";

/**
 *
 * @param message {string}
 * @returns {JSX.Element|null}
 * @constructor
 */
export const FieldErrorMessage = ({ message }) => {
  return <Form.Text className="text-danger">{message}</Form.Text>;
};

/**
 * データ操作系apiのエラーメッセージを表示するためのコンポーネント
 * @param message {string|object}
 * @returns {JSX.Element}
 */
export const APIErrorMessage = ({ errorResponse }) => {
  // パラメーター不正はerrorResponse.data?.params、
  // 他の403などのエラーは errorResponse.message を使う
  const messages = errorResponse.data?.params || errorResponse.message;

  if (errorResponse.data?.error) {
    console.error("file:", errorResponse.data.error.file);
    console.error("message:", errorResponse.data.error.message);
  }

  if (typeof messages === "string") {
    return <FieldErrorMessage message={messages} />;
  } else {
    return (
      <>
        {Object.entries(messages).map(([key, value]) => (
          <p>
            <FieldErrorMessage message={`${key}: ${value}`} />
          </p>
        ))}
      </>
    );
  }
};
