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
import { BookingOnSite } from "./pages/BookingOnSite";
import { Completion } from "./pages/Completion";
import { SelectPeriod } from "./pages/SelectPeriod";

const BookingRoutes = () => {
  const eventData = window.wpYoyakuEventData;

  if (eventData.error_message) {
    return <p className="text-danger">{eventData.error_message}</p>;
  }

  return (
    <App>
      <Routes>
        <Route path="/" element={<SelectPeriod eventData={eventData} />} />
        <Route
          path="/:uuid"
          element={<BookingOnSite eventData={eventData} />}
        />
        <Route path="/completion" element={<Completion />} />
      </Routes>
    </App>
  );
};

document.addEventListener("DOMContentLoaded", () => {
  const domName = "yoyaku-block-event";
  if (
    "undefined" !== typeof document.getElementById(domName) &&
    null !== document.getElementById(domName)
  ) {
    const root = createRoot(document.getElementById(domName));
    root.render(
      <StrictMode>
        <BookingRoutes />
      </StrictMode>,
    );
  }
});
