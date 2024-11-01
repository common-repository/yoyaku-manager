<?php declare(strict_types=1);

namespace Yoyaku\Application\Routes;

use WP_REST_Server;
use Yoyaku\Application\Routes\Common\RESTController;
use Yoyaku\Domain\Payment\GatewayType;

class Payment extends RESTController
{
    protected string $rest_base = 'payments';

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/refund/(?P<transaction_id>[\d]+)',
            [
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'permission_callback' => fn() => current_user_can('yoyaku_write_events'),
                    'callback' => [$this->controller, 'refund'],
                    'gateway' => [
                        'type' => 'string',
                        'required' => true,
                        'enum' => GatewayType::values(),
                    ],
                ]
            ]
        );
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/stripe/webhook',
            [
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'permission_callback' => '__return_true',
                    'callback' => [$this->controller, 'webhook_stripe'],
                ]
            ]
        );

        // $app->post('/payments/amount', CalculatePaymentAmountController::class);
        // $app->get('/payments/transaction/{id:[0-9]+}', GetTransactionAmountController::class);

    }
}
