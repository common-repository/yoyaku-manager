/**
 * このファイルは、このブロックを含む投稿/ページのフロントエンドで実行される。
 * "block.json"の "viewScript"プロパティの値として定義すると、
 * サイトのフロントエンドのキューに入れられる。
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */
import { App } from "@/gutenberg-blocks/components/layout";
import { createRoot } from "@wordpress/element";
import { StrictMode } from "react";
import { Route, Routes } from "react-router-dom";
import { CancelEvent } from "./pages/CancelEvent";
import { Completion } from "./pages/Completion";

const BookingCancelRoutes = () => {
  return (
    <App>
      <Routes>
        <Route path="/" element={<CancelEvent />} />
        <Route path="/completion" element={<Completion />} />
      </Routes>
    </App>
  );
};

document.addEventListener("DOMContentLoaded", () => {
  const domName = "yoyaku-block-cancel-booking";
  if (
    "undefined" !== typeof document.getElementById(domName) &&
    null !== document.getElementById(domName)
  ) {
    const root = createRoot(document.getElementById(domName));
    root.render(
      <StrictMode>
        <BookingCancelRoutes />
      </StrictMode>,
    );
  }
});
