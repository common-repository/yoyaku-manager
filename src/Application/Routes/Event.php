<?php declare(strict_types=1);

namespace Yoyaku\Application\Routes;

use WP_REST_Server;
use Yoyaku\Application\Routes\Common\RESTController;
use Yoyaku\Domain\ValueObject\String\Description;
use Yoyaku\Domain\ValueObject\String\Name;


class Event extends RESTController
{
    protected string $rest_base = 'events';

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
    }

    public function get_collection_params()
    {
        $query_params = parent::get_collection_params();

        $query_params['orderby'] = [
            'type' => 'string',
            'enum' => ['name'],
        ];

        $query_params['order'] = [
            'type' => 'string',
            'enum' => ['asc', 'desc'],
        ];

        return $query_params;
    }

    public function get_calendar_params()
    {
        $query_params = [
            'date_from' => [
                'type' => 'string',
                'format' => 'date',
            ],
            'date_to' => [
                'type' => 'string',
                'format' => 'date',
            ]
        ];

        return $query_params;
    }

    public function get_item_schema()
    {
        if ($this->schema) {
            return $this->schema;
        }

        $schema = [
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            'title' => 'event',
            'type' => 'object',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'format' => 'text-field',
                    'required' => true,
                    'minLength' => 1,
                    'maxLength' => Name::MAX_LENGTH,
                ],
                'use_approval_system' => [
                    'type' => 'boolean',
                    'required' => true,
                ],
                'min_time_to_close_booking' => [
                    'type' => 'integer',
                    'required' => true,
                    'minimum' => 0,
                ],
                'min_time_to_cancel_booking' => [
                    'type' => 'integer',
                    'required' => true,
                    'minimum' => 0,
                ],
                'max_tickets_per_booking' => [
                    'type' => 'integer',
                    'required' => true,
                    'minimum' => 1,
                ],
                'is_online_payment' => [
                    'type' => 'boolean',
                    'default' => false,
                ],
                'redirect_url' => [
                    'type' => 'string',
                    'required' => true,
                    'format' => 'uri',
                ],
                'description' => [
                    'type' => 'string',
                    'format' => 'textarea-field',
                    'maxLength' => Description::MAX_LENGTH,
                ],
                'notification_ids' => [
                    'type' => 'array',
                    'required' => true,
                    'items' => [
                        'type' => 'integer',
                    ],
                ],
            ]
        ];

        $this->schema = $schema;
        return $this->schema;
    }
}
