<?php declare(strict_types=1);

namespace Yoyaku\Application\Routes;

use WP_REST_Server;
use Yoyaku\Application\Routes\Common\Patterns;
use Yoyaku\Application\Routes\Common\RESTController;
use Yoyaku\Domain\Notification\Content;
use Yoyaku\Domain\Notification\NotificationTiming;
use Yoyaku\Domain\ValueObject\String\Name;

class Notification extends RESTController
{
    protected string $rest_base = 'notifications';

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this->controller, 'get_items'],
                    'permission_callback' => fn() => current_user_can('yoyaku_read_notifications'),
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
                    'permission_callback' => fn() => current_user_can('yoyaku_read_notifications'),
                ],
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this->controller, 'update_item'],
                    'permission_callback' => fn() => current_user_can('yoyaku_write_notifications'),
                    'args' => $this->get_endpoint_args_for_item_schema(WP_REST_Server::EDITABLE),
                ],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/send',
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this->controller, 'send_scheduled_mails'],
                    'permission_callback' => '__return_true',
                ],
            ]
        );
    }

    public function get_collection_params()
    {
        $query_params['orderby'] = [
            'type' => 'string',
            'default' => 'timing',
            'enum' => ['timing', 'name'],
        ];

        $query_params['order'] = [
            'type' => 'string',
            'enum' => ['asc', 'desc'],
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
            'title' => 'notification',
            'type' => 'object',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'format' => 'text-field',
                    'required' => true,
                    'minLength' => 1,
                    'maxLength' => Name::MAX_LENGTH,
                ],
                'subject' => [
                    'type' => 'string',
                    'format' => 'text-field',
                    'required' => true,
                    'minLength' => 1,
                    'maxLength' => Name::MAX_LENGTH,
                ],
                // sanitize_textarea_field はhtmlタグが消されるためsanitize_callbackはデフォルトのものを使う
                'content' => [
                    'type' => 'string',
                    'required' => true,
                    'maxLength' => Content::MAX_LENGTH,
                ],
                'timing' => [
                    'type' => 'string',
                    'required' => true,
                    'enum' => NotificationTiming::values(),
                ],
                'days' => [
                    'type' => ['integer', 'null'],
                    'minimum' => 0,
                ],
                'time' => [
                    'type' => ['string', 'null'],
                    'format' => 'text-field',
                    'pattern' => Patterns::$time,
                ],
                'is_before' => [
                    'type' => 'boolean',
                ],
            ]
        ];

        $this->schema = $schema;
        return $this->schema;
    }
}
