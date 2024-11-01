import {settings} from "./settings";

/**
 * プラグインの設定に応じたフォーマット価格を取得
 * @param price
 * @returns {string}
 */
export const getFormattedPrice = (price) => {
  const symbolPrefix =
    settings.price_symbol_position === "before" ? settings.symbol : "";
  const symbolSuffix =
    settings.price_symbol_position === "after" ? settings.symbol : "";
  const integerPart = numberWithSeparator(
    Math.trunc(price),
    settings.price_thousand_separator,
  );
  const decimalPlaces = settings.price_decimals;
  const decimalPart =
    0 < decimalPlaces
      ? settings.price_decimal_separator +
        Math.abs(price - Math.trunc(price))
          .toFixed(settings.price_decimals)
          .slice(2)
      : "";

  return symbolPrefix + integerPart + decimalPart + symbolSuffix;
};

/**
 * 1000000 を 1,000,000 の様に変換する
 * @param number {int}
 * @param separator 千桁の区切り文字
 * @returns {string}
 */
function numberWithSeparator(number, separator) {
  return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, separator);
}

/**
 * 支払い金額を更新する（ブロック用）
 * @param buyTickets {array} フォームのticketsのデータ
 * @returns {number}
 */
export const getTotalPrice = (buyTickets) => {
  let total = 0;
  buyTickets.forEach((buyTicket) => {
    total += buyTicket.buy_count * buyTicket.price;
  });
  return total;
};

/**
 * 金額を通貨の最小単位に変換する（ブロック用）
 * stripeのAPIリクエストの金額は各通貨の最小単位にする必要がある
 * https://docs.stripe.com/currencies
 * @param amount
 * @param currency
 * @return int
 */
export const parseMoneyAmount = (amount, currency) => {
  const currencyUpper = currency.toUpperCase();
  if (
    [
      "BIF",
      "CLP",
      "DJF",
      "GNF",
      "JPY",
      "KMF",
      "KRW",
      "MGA",
      "PYG",
      "RWF",
      "UGX",
      "VND",
      "VUV",
      "XAF",
      "XOF",
      "XPF",
    ].includes(currencyUpper)
  ) {
    // 小数点以下のない通貨
    return amount;
  } else if (["BHD", "JOD", "KWD", "OMR", "TND"].includes(currencyUpper)) {
    // 小数数点以下が3桁の通貨
    return Math.floor(amount * 1000);
  } else {
    // 小数数点以下が2桁の通貨
    return Math.floor(amount * 100);
  }
};
