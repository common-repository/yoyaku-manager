<?php declare(strict_types=1);

namespace Yoyaku\Application\Routes;

use WP_REST_Server;
use Yoyaku\Application\Routes\Common\RESTController;
use Yoyaku\Domain\Customer\Gender;
use Yoyaku\Domain\Customer\ZipCode;
use Yoyaku\Domain\EventType\EventBooking\BookingStatus;
use Yoyaku\Domain\EventType\EventBooking\Memo;
use Yoyaku\Domain\Payment\GatewayType;
use Yoyaku\Domain\Payment\PaymentStatus;
use Yoyaku\Domain\ValueObject\String\Address;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Domain\ValueObject\String\Phone;

class EventBooking extends RESTController
{
    protected string $rest_base = 'event-bookings';

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this->controller, 'get_items'],
                    'permission_callback' => fn() => current_user_can('yoyaku_read_events'),
                    'args' => $this->get_collection_params(),
                ],
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this->controller, 'add_item'],
                    'permission_callback' => fn() => current_user_can('yoyaku_write_events'),
                    'args' => array_merge(
                        [
                            'event_period_id' => [
                                'type' => 'integer',
                                'required' => true,
                            ],
                            'gateway' => [
                                'type' => 'string',
                                'required' => true,
                                'enum' => GatewayType::values(),
                                'default' => GatewayType::ON_SITE->value,
                            ],
                            'payment_status' => [
                                'type' => 'string',
                                'required' => true,
                                'enum' => PaymentStatus::values(),
                            ],
                            'memo' => [
                                'type' => 'string',
                                'format' => 'textarea-field',
                                'maxLength' => Memo::MAX_LENGTH,
                            ],
                        ],
                        self::get_create_tickets_args(),
                        self::get_customer_args(),
                    ),
                ]
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            [
                'args' => [
                    'id' => [
                        'type' => 'integer',
                    ],
                ],
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this->controller, 'get_item'],
                    'permission_callback' => fn() => current_user_can('yoyaku_read_events'),
                ],
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this->controller, 'update_item'],
                    'permission_callback' => fn() => current_user_can('yoyaku_write_events'),
                    'args' => array_merge(
                        [
                            'worker' => [
                                'type' => 'string',
                                'format' => 'text-field',
                                'maxLength' => Name::MAX_LENGTH,
                            ],
                            'payment_status' => [
                                'type' => 'string',
                                'enum' => PaymentStatus::values(),
                            ],
                            'memo' => [
                                'type' => 'string',
                                'format' => 'textarea-field',
                                'maxLength' => Memo::MAX_LENGTH,
                            ],
                            'update_customer' => [
                                'type' => 'boolean',
                                'default' => false
                            ],
                            'tickets' => [
                                'type' => 'array',
                                'required' => true,
                                'uniqueItems' => true,
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => [
                                            'type' => 'integer',
                                            'required' => true,
                                        ],
                                        'buy_count' => [
                                            'type' => 'integer',
                                            'required' => true,
                                            'minimum' => 0,
                                        ],
                                    ]
                                ],
                            ],
                        ],
                        self::get_customer_args(WP_REST_Server::EDITABLE),
                    )
                ],
                [
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => [$this->controller, 'delete_item'],
                    'permission_callback' => fn() => current_user_can('yoyaku_delete_events'),
                ],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/status/(?P<id>[\d]+)',
            [
                'args' => [
                    'id' => [
                        'type' => 'integer',
                    ],
                ],
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this->controller, 'update_item_status'],
                    'permission_callback' => fn() => current_user_can('yoyaku_write_events'),
                    'args' => [
                        'status' => [
                            'type' => 'string',
                            'required' => true,
                            'enum' => BookingStatus::values(),
                        ],
                    ],
                ],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/front/' . $this->rest_base,
            [
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this->controller, 'front_add_item'],
                    'permission_callback' => '__return_true',
                    'args' => array_merge(
                        [
                            //イベント予約用パラメーター
                            'event_period_uuid' => [
                                'type' => 'string',
                                'format' => 'uuid',
                                'required' => true,
                            ],
                            'gateway' => [
                                'type' => 'string',
                                'required' => true,
                                'enum' => GatewayType::values(),
                            ],
                            'confirmation_token_id' => [
                                'type' => 'string',
                                'format' => 'text-field',
                            ],
                            'memo' => [
                                'type' => 'string',
                                'format' => 'textarea-field',
                                'maxLength' => Memo::MAX_LENGTH,
                            ],
                            'captcha_value' => [
                                'type' => 'string',
                                'format' => 'textarea-field',
                                'default' => '',
                            ],
                        ],
                        self::get_create_tickets_args(),
                        self::get_customer_args(),
                    ),
                ],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/front/' . $this->rest_base . '/cancel',
            [
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this->controller, 'front_cancel_item'],
                    'permission_callback' => '__return_true',
                    'args' => [
                        'token' => [
                            'type' => 'string',
                            'format' => 'text-field',
                            'required' => true,
                        ],
                    ],
                ],
            ]
        );
    }

    public function get_collection_params()
    {
        $query_params = parent::get_collection_params();
        $query_params['event_id'] = [
            'type' => 'integer',
        ];
        $query_params['event_period_id'] = [
            'type' => 'integer',
        ];

        return $query_params;
    }

    /**
     * 予約追加に必要な顧客のパラメーター
     * @return array
     */
    public static function get_customer_args($method = WP_REST_Server::CREATABLE)
    {
        $required = $method === WP_REST_Server::CREATABLE;
        return [
            'email' => [
                'type' => 'string',
                'format' => 'email',
                'required' => $required,
                'maxLength' => Email::MAX_LENGTH,
            ],
            'first_name' => [
                'type' => 'string',
                'format' => 'text-field',
                'required' => $required,
                'minLength' => 1,
                'maxLength' => Name::MAX_LENGTH,
            ],
            'last_name' => [
                'type' => 'string',
                'format' => 'text-field',
                'required' => $required,
                'minLength' => 1,
                'maxLength' => Name::MAX_LENGTH,
            ],
            'first_name_ruby' => [
                'type' => 'string',
                'format' => 'text-field',
                'maxLength' => Name::MAX_LENGTH,
            ],
            'last_name_ruby' => [
                'type' => 'string',
                'format' => 'text-field',
                'maxLength' => Name::MAX_LENGTH,
            ],
            'phone' => [
                'type' => 'string',
                'format' => 'text-field',
                'maxLength' => Phone::MAX_LENGTH,
            ],
            'zipcode' => [
                'type' => 'string',
                'format' => 'text-field',
                'maxLength' => ZipCode::MAX_LENGTH,
            ],
            'address' => [
                'type' => 'string',
                'format' => 'text-field',
                'maxLength' => Address::MAX_LENGTH,
            ],
            'birthday' => [
                'type' => ['string', 'null'],
                'format' => 'date',
                'example' => '2020-01-31',
            ],
            'gender' => [
                'type' => 'string',
                'enum' => Gender::values(),
            ],
        ];
    }

    /**
     * 予約追加に必要なチケットのパラメーター
     * @return array
     */
    private static function get_create_tickets_args()
    {
        return [
            'tickets' => [
                'type' => 'array',
                'required' => true,
                'uniqueItems' => true,
                "minItems" => 1,
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'integer',
                            'required' => true,
                        ],
                        'buy_count' => [
                            'type' => 'integer',
                            'required' => true,
                            'minimum' => 0,
                        ],
                    ]
                ],
            ],
        ];
    }
}
