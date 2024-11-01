<?php declare(strict_types=1);

namespace Yoyaku\Application\Routes\Common;

use WP_REST_Server;
use Yoyaku\Application\Common\AController;

/**
 * （参考）WP_REST_Controller クラス, rest-api.php
 */
abstract class RESTController
{
    /**
     * The namespace of this controller's route.
     * @var string
     */
    protected string $namespace = YOYAKU_ROUTE_NAMESPACE;

    /**
     * The base of this controller's route.
     * @var string
     */
    protected string $rest_base = '';

    /**
     * Cached results of get_item_schema.
     * @var array
     */
    protected array $schema = [];

    /**
     * コントローラークラス
     * @var AController
     */
    protected AController $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    /**
     * apiを登録する
     * データ更新系のmethodsは'POST'にする
     */
    abstract protected function register_routes();

    /**
     * コレクションのクエリパラメータを取得する
     * @return array コレクションのクエリパラメータ
     */
    public function get_collection_params()
    {
        return [
            'page' => [
                'description' => 'Current page of the collection.',
                'type' => 'integer',
                'default' => 1,
                'minimum' => 1,
                'sanitize_callback' => 'rest_sanitize_request_arg',
                'validate_callback' => 'rest_validate_request_arg',
            ],
            'per_page' => [
                'description' => 'Maximum number of items to be returned in result set.',
                'type' => 'integer',
                'default' => 50,
                'minimum' => 1,
                'maximum' => 100,
                'sanitize_callback' => 'rest_sanitize_request_arg',
                'validate_callback' => 'rest_validate_request_arg',
            ],
            'search' => [
                'description' => 'Limit results to those matching a string.',
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'rest_validate_request_arg',
            ],
        ];
    }

    /**
     * JSONスキーマに準拠した項目のスキーマを取得
     * @return array Item schema data.
     */
    public function get_item_schema()
    {
        return [];
    }

    /**
     * @param string $method Optional. リクエストのHTTPメソッド
     * @return array Endpoint arguments.
     */
    public function get_endpoint_args_for_item_schema(string $method = WP_REST_Server::CREATABLE)
    {
        return rest_get_endpoint_args_for_schema($this->get_item_schema(), $method);
    }
}
