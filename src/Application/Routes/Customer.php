<?php declare(strict_types=1);

namespace Yoyaku\Application\Routes;

use WP_REST_Server;
use Yoyaku\Application\Routes\Common\Patterns;
use Yoyaku\Application\Routes\Common\RESTController;
use Yoyaku\Domain\Customer\Gender;
use Yoyaku\Domain\Customer\ZipCode;
use Yoyaku\Domain\ValueObject\String\Address;
use Yoyaku\Domain\ValueObject\String\Description;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Domain\ValueObject\String\Phone;


class Customer extends RESTController
{
    protected string $rest_base = 'customers';

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this->controller, 'get_items'],
                    'permission_callback' => fn() => current_user_can('yoyaku_read_customers'),
                    'args' => $this->get_collection_params(),
                ],
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this->controller, 'add_item'],
                    'permission_callback' => fn() => current_user_can('yoyaku_write_customers'),
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
                    'permission_callback' => fn() => current_user_can('yoyaku_read_customers'),
                ],
                [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this->controller, 'update_item'],
                    'permission_callback' => fn() => current_user_can('yoyaku_write_customers'),
                    'args' => $this->get_endpoint_args_for_item_schema(WP_REST_Server::EDITABLE),
                ],
                [
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => [$this->controller, 'delete_item'],
                    'permission_callback' => fn() => current_user_can('yoyaku_delete_customers'),
                ]
            ]
        );
    }

    public function get_collection_params()
    {
        $query_params = parent::get_collection_params();

        $query_params['orderby'] = [
            'type' => 'string',
            'enum' => ['id', 'name', 'email'],
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
            'title' => 'customer',
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'format' => 'email',
                    'required' => true,
                    'maxLength' => Email::MAX_LENGTH,
                ],
                'first_name' => [
                    'type' => 'string',
                    'format' => 'text-field',
                    'required' => true,
                    'minLength' => 1,
                    'maxLength' => Name::MAX_LENGTH,
                ],
                'last_name' => [
                    'type' => 'string',
                    'format' => 'text-field',
                    'required' => true,
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
                    'maxLength' => Phone::MAX_LENGTH,
                    'pattern' => Patterns::$phone,
                ],
                'gender' => [
                    'type' => 'string',
                    'enum' => Gender::values(),
                ],
                'birthday' => [
                    'type' => ['string', 'null'],
                    'format' => 'date',
                    'example' => '2020-01-31',
                ],
                'zipcode' => [
                    'type' => 'string',
                    'format' => 'text-field',
                    'default' => '',
                    'maxLength' => ZipCode::MAX_LENGTH,
                ],
                'address' => [
                    'type' => 'string',
                    'format' => 'text-field',
                    'default' => '',
                    'maxLength' => Address::MAX_LENGTH,
                ],
                'memo' => [
                    'type' => 'string',
                    'format' => 'textarea-field',
                    'maxLength' => Description::MAX_LENGTH,
                ],
            ]
        ];

        $this->schema = $schema;
        return $this->schema;
    }
}
