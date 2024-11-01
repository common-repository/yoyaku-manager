<?php declare(strict_types=1);

namespace Yoyaku\Application\Routes;

use WP_REST_Server;
use Yoyaku\Application\Routes\Common\RESTController;

class EmailLog extends RESTController
{
    protected string $rest_base = 'emaillogs';

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this->controller, 'get_items'],
                    'permission_callback' => fn() => current_user_can('yoyaku_read_emaillogs'),
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
                    'permission_callback' => fn() => current_user_can('yoyaku_read_emaillogs'),
                ],
                [
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => [$this->controller, 'delete_item'],
                    'permission_callback' => fn() => current_user_can('yoyaku_delete_emaillogs'),
                ]
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/send-undelivered',
            [
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this->controller, 'send_undelivered_emails'],
                    'permission_callback' => fn() => current_user_can('yoyaku_write_emaillogs'),
                    'args' => [
                        'id' => [
                            'type' => 'integer',
                            'minimum' => 1,
                        ],
                    ],
                ]
            ]
        );
    }

    public function get_collection_params()
    {
        $query_params = parent::get_collection_params();

        $query_params['sent'] = [
            'type' => 'boolean',
        ];

        return $query_params;
    }
}
