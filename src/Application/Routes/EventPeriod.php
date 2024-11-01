<?php declare(strict_types=1);

namespace Yoyaku\Application\Routes;

use WP_REST_Server;
use Yoyaku\Application\Routes\Common\RESTController;
use Yoyaku\Domain\ValueObject\String\Address;
use Yoyaku\Domain\ValueObject\String\Url;

class EventPeriod extends RESTController
{
    protected string $rest_base = 'event-periods';

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
            '/' . $this->rest_base . '/(?P<uuid>[a-zA-Z0-9-]+)',
            [
                'args' => [
                    'uuid' => [
                        'type' => 'string',
                        'format' => 'uuid',
                    ],
                ],
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this->controller, 'get_item'],
                    'permission_callback' => fn() => current_user_can('yoyaku_read_events'),
                ],
            ]
        );

    }

    public function get_collection_params()
    {
        $query_params = parent::get_collection_params();
        $query_params['event_id'] = [
            'type' => 'integer',
            'required' => true,
        ];
        $query_params['show_past'] = [
            'type' => 'boolean',
            'default' => false,
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
            'title' => 'event-period',
            'type' => 'object',
            'properties' => [
                'event_id' => [
                    'type' => 'integer',
                    'required' => true,
                ],
                'wp_id' => [
                    'type' => ['integer', 'null'],
                ],
                'start_datetime' => [
                    'type' => 'string',
                    'required' => true,
                    'format' => 'date-time',
                ],
                'end_datetime' => [
                    'type' => 'string',
                    'required' => true,
                    'format' => 'date-time',
                ],
                'location' => [
                    'type' => 'string',
                    'format' => 'text-field',
                    'default' => '',
                    'maxLength' => Address::MAX_LENGTH,
                ],
                'max_ticket_count' => [
                    'type' => 'integer',
                    'required' => true,
                    'minimum' => 1,
                ],
                'online_meeting_url' => [
                    'type' => 'string',
                    'format' => 'uri',
                    'maxLength' => Url::MAX_LENGTH,
                ],
            ]
        ];

        $this->schema = $schema;
        return $this->schema;
    }
}
