<?php declare(strict_types=1);

namespace Yoyaku\Application\Routes;

use WP_Error;
use WP_REST_Request;
use WP_REST_Server;
use Yoyaku\Application\Routes\Common\RESTController;
use Yoyaku\Domain\Payment\CurrencyService;
use Yoyaku\Domain\Setting\OptionFieldStatus;

class Settings extends RESTController
{
    protected string $rest_base = 'settings';

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this->controller, 'get_settings'],
                    'permission_callback' => fn() => current_user_can('manage_options'),
                ],
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this->controller, 'update_settings'],
                    'permission_callback' => fn() => current_user_can('manage_options'),
                    'args' => $this->get_endpoint_args_for_item_schema(WP_REST_Server::EDITABLE),
                ],
            ],
        );

        register_rest_route(
            $this->namespace,
            '/front/' . $this->rest_base,
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this->controller, 'get_front_settings'],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function get_item_schema()
    {
        if ($this->schema) {
            return $this->schema;
        }

        $schema = [
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            'title' => 'settings',
            'type' => 'object',
            'properties' => [
                // activation
                'yoyaku_license_key' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],

                // general
                'default_country_code' => [
                    'type' => 'string',
                    'format' => 'text-field',
                    'maxLength' => 2,
                ],
                'phone_field_status' => [
                    'type' => 'string',
                    'enum' => OptionFieldStatus::values(),
                ],
                'ruby_field_status' => [
                    'type' => 'string',
                    'enum' => OptionFieldStatus::values(),
                ],
                'birthday_field_status' => [
                    'type' => 'string',
                    'enum' => OptionFieldStatus::values(),
                ],
                'zipcode_field_status' => [
                    'type' => 'string',
                    'enum' => OptionFieldStatus::values(),
                ],
                'address_field_status' => [
                    'type' => 'string',
                    'enum' => OptionFieldStatus::values(),
                ],
                'gender_field_status' => [
                    'type' => 'string',
                    'enum' => OptionFieldStatus::values(),
                ],
                'terms_of_service_url' => [
                    'type' => 'string',
                    'format' => 'uri',
                ],
                'delete_content' => [
                    'type' => 'boolean',
                ],

                // notification
                'email_service' => [
                    'type' => 'string',
                    'enum' => ['wp_mail', 'smtp'],
                ],
                'smtp_host' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],
                'smtp_port' => [
                    'type' => 'integer',
                    'minimum' => 0,
                    'maximum' => 65535,
                ],
                'smtp_secure' => [
                    'type' => 'string',
                    'enum' => ['ssl', 'tls'],
                ],
                'smtp_username' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],
                'smtp_password' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],
                'sender_name' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],
                'sender_email' => [
                    'type' => 'string',
                    'format' => 'email',
                    'arg_options' => [
                        'sanitize_callback' => [$this, 'check_email'],
                        'validate_callback' => null, // Skip built-in validation of 'email'.
                    ]
                ],
                'bcc_email' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],
                'cancel_url' => [
                    'type' => 'string',
                    'format' => 'uri',
                ],

                // payment
                'currency' => [
                    'type' => 'string',
                    'enum' => array_merge([""], CurrencyService::get_currency_ISO_list()),
                ],
                'symbol' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],
                'price_symbol_position' => [
                    'type' => 'string',
                    'enum' => ['before', 'after'],
                ],
                'price_decimals' => [
                    'type' => 'integer',
                    'minimum' => 0,
                ],
                'price_thousand_separator' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],
                'price_decimal_separator' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],

                // stripe
                'stripe_test_mode' => [
                    'type' => 'boolean',
                ],
                'stripe_live_secret_key' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],
                'stripe_test_secret_key' => [
                    'type' => 'string',
                ],

                // google recaptcha
                'google_recaptcha_site_key' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],

                'google_recaptcha_secret_key' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],

                // google calendar & meet
                'google_client_id' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],
                'google_client_secret' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],
                'google_show_attendees' => [
                    'type' => 'boolean',
                ],
                'google_add_attendees' => [
                    'type' => 'boolean',
                ],
                'google_send_event_invitation_email' => [
                    'type' => 'boolean',
                ],
                'google_maximum_number_of_events_returned' => [
                    'type' => 'integer',
                ],
                'google_status' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],
                'google_enable_google_meet' => [
                    'type' => 'boolean',
                ],

                // zoom
                'zoom_account_id' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],
                'zoom_client_id' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],
                'zoom_client_secret' => [
                    'type' => 'string',
                    'format' => 'text-field',
                ],
            ]
        ];

        $this->schema = $schema;
        return $this->schema;
    }

    /**
     * メールアドレスか、空文字のチェックをする
     * 参考) class-wp-rest-cmments-controller.php check_comment_author_email()
     * @param string $value email value.
     * @param WP_REST_Request $request Full details about the request.
     * @param string $param The parameter name.
     * @return string|WP_Error The sanitized email address, if valid, otherwise an error.
     */
    public function check_email($value, $request, $param)
    {
        $email = (string)$value;
        if (empty($email)) {
            return $email;
        }

        $check_email = rest_validate_request_arg($email, $request, $param);
        if (is_wp_error($check_email)) {
            return $check_email;
        }

        return $email;
    }
}
