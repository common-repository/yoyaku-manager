<?php declare(strict_types=1);

namespace Yoyaku\Application\Routes;

use WP_REST_Server;
use Yoyaku\Application\Routes\Common\RESTController;
use Yoyaku\Domain\ValueObject\String\Name;

class EventTicket extends RESTController
{
    protected string $rest_base = 'event-tickets';

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
                    'args' => $this->get_endpoint_args_for_item_schema(),
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
                    'args' => $this->get_endpoint_args_for_item_schema(WP_REST_Server::EDITABLE),
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
            '/front/' . $this->rest_base,
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this->controller, 'get_items_with_sold_count_for_front'],
                    'permission_callback' => '__return_true',
                    'args' => [
                        'event_period_uuid' => [
                            'type' => 'string',
                            'format' => 'uuid',
                            'required' => true,
                        ],
                    ],
                ],
            ]
        );
    }

    public function get_collection_params()
    {
        return [
            'event_id' => [
                'type' => 'integer',
            ],

            'event_period_id' => [
                'type' => 'integer',
            ],

            'event_booking_id' => [
                'type' => 'integer',
            ],

            'with_sold_count' => [
                'type' => 'boolean',
                'default' => false
            ],
        ];
    }

    public function get_item_schema()
    {
        if ($this->schema) {
            return $this->schema;
        }

        $schema = [
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            'title' => 'event-ticket',
            'type' => 'object',
            'properties' => [
                'event_id' => [
                    'type' => 'integer',
                    'required' => true,
                ],
                'name' => [
                    'type' => 'string',
                    'format' => 'text-field',
                    'required' => true,
                    'minLength' => 1,
                    'maxLength' => Name::MAX_LENGTH,
                ],
                'price' => [
                    'type' => 'number',
                    'required' => true,
                    'minimum' => 0,
                ],
                'ticket_count' => [
                    'type' => 'integer',
                    'required' => true,
                    'minimum' => 0,
                ],
            ]
        ];

        $this->schema = $schema;
        return $this->schema;
    }
}
