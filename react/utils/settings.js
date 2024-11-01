import { optionFieldStatus as status } from "./consts";

const settings = {
  address_field_status: status.hidden,
  birthday_field_status: status.hidden,
  capability: { can_read: false, can_write: false, can_delete: false },
  currency: "",
  current_user_id: 0,
  default_country_code: "",
  gender_field_status: status.hidden,
  google_calendar: null,
  google_recaptcha_secret_key: "",
  google_recaptcha_site_key: "",
  phone_field_status: status.hidden,
  price_decimal_separator: ".",
  price_decimals: 0,
  price_symbol_position: "before",
  price_thousand_separator: ",",
  ruby_field_status: status.hidden,
  stripe_publishable_key: "",
  symbol: "",
  terms_of_service_url: "",
  yoyaku_is_activated: 0,
  zipcode_field_status: status.hidden,
  zoom_is_active: 0,

  init: function (values) {
    // 存在チェック
    this.phone_field_status = values.phone_field_status;
    this.address_field_status = values.address_field_status;
    this.birthday_field_status = values.birthday_field_status;
    this.capability = values.capability;
    this.currency = values.currency;
    this.current_user_id = parseInt(values.current_user_id);
    this.default_country_code = values.default_country_code;
    this.gender_field_status = values.gender_field_status;
    this.google_calendar = values.google_calendar === "1";
    this.google_recaptcha_secret_key = values.google_recaptcha_secret_key;
    this.google_recaptcha_site_key = values.google_recaptcha_site_key;
    this.phone_field_status = values.phone_field_status;
    this.price_decimal_separator = values.price_decimal_separator;
    this.price_decimals = parseInt(values.price_decimals);
    this.price_symbol_position = values.price_symbol_position;
    this.price_thousand_separator = values.price_thousand_separator;
    this.ruby_field_status = values.ruby_field_status;
    this.stripe_publishable_key = values.stripe_publishable_key;
    this.symbol = values.symbol;
    this.terms_of_service_url = values.terms_of_service_url;
    this.yoyaku_is_activated = values.yoyaku_is_activated === "1";
    this.zipcode_field_status = values.zipcode_field_status;
    this.zoom_is_active = values.zoom_is_active === "1";
  },

  set: function (key, value) {
    // 存在チェック
    this[key] = value;
  },

  getOptionFieldSettings: function () {
    return {
      phone: this.phone_field_status,
      phoneIsRequired: this.phone_field_status === status.required,
      phoneIsHidden: this.phone_field_status === status.hidden,

      ruby: this.ruby_field_status,
      rubyIsRequired: this.ruby_field_status === status.required,
      rubyIsHidden: this.ruby_field_status === status.hidden,

      birthday: this.birthday_field_status,
      birthdayIsRequired: this.birthday_field_status === status.required,
      birthdayIsHidden: this.birthday_field_status === status.hidden,

      zipcode: this.zipcode_field_status,
      zipcodeIsRequired: this.zipcode_field_status === status.required,
      zipcodeIsHidden: this.zipcode_field_status === status.hidden,

      address: this.address_field_status,
      addressIsRequired: this.address_field_status === status.required,
      addressIsHidden: this.address_field_status === status.hidden,

      gender: this.gender_field_status,
      genderIsRequired: this.gender_field_status === status.required,
      genderIsHidden: this.gender_field_status === status.hidden,
    };
  },

  canRead: function () {
    return this.capability.can_read;
  },
  canWrite: function () {
    return this.capability.can_write;
  },
  canDelete: function () {
    return this.capability.can_delete;
  },
};

// wp_localize_script()で定義したjsの変数データで初期化
if (window?.wpYoyakuSettings) {
  settings.init(window.wpYoyakuSettings);
}

export { settings };
