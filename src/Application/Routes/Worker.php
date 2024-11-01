<?php declare(strict_types=1);

namespace Yoyaku\Application\Routes;

use WP_REST_Server;
use Yoyaku\Application\Routes\Common\RESTController;
use Yoyaku\Domain\ValueObject\String\Name;


class Worker extends RESTController
{
    protected string $rest_base = 'workers';

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this->controller, 'get_items'],
                    'permission_callback' => fn() => current_user_can('yoyaku_read_workers'),
                    'args' => $this->get_collection_params(),
                ],
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
                    'permission_callback' => fn() => current_user_can('yoyaku_read_workers'),
                ],
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this->controller, 'update_item'],
                    'permission_callback' => fn() => current_user_can('yoyaku_write_workers'),
                    'args' => $this->get_endpoint_args_for_item_schema(),
                ]
            ]
        );
    }

    public function get_collection_params()
    {
        return [
            'page' => [
                'type' => 'integer',
                'minimum' => 1,
            ],
            'per_page' => [
                'type' => 'integer',
                'minimum' => 1,
                'maximum' => 100,
            ],
            'orderby' => [
                'type' => 'string',
                'enum' => ['display_name'],
            ],
            'order' => [
                'type' => 'string',
                'enum' => ['asc', 'desc'],
                'default' => 'asc',
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
            'title' => 'worker',
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'integer',
                    'required' => true,
                ],
                'zoom_user_id' => [
                    'type' => 'string',
                    'maxLength' => Name::MAX_LENGTH,
                ],
            ]
        ];

        $this->schema = $schema;
        return $this->schema;
    }
}
