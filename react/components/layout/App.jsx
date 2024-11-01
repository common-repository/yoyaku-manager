import { fetcher } from "@/utils/fetcher";
import { Toaster } from "react-hot-toast";
import { HashRouter } from "react-router-dom";
import { SWRConfig } from "swr";
import "@/assets/style.scss";

export const App = (props) => {
  return (
    <SWRConfig
      value={{
        revalidateOnFocus: false, // ブラウザをアクティブにする毎にデータを再取得する機能
        fetcher: fetcher, // Configで設定することでuseSWR()の第２引数のfetcherが不要になる
      }}
    >
      <HashRouter future={{ v7_startTransition: true }}>
        {props.children}
        <Toaster
          containerStyle={{ top: 40, left: 20, bottom: 20, right: 20 }}
        />
      </HashRouter>
    </SWRConfig>
  );
};
