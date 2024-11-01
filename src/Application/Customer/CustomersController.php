<?php declare(strict_types=1);

namespace Yoyaku\Application\Customer;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Yoyaku\Application\Common\AController;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\DuplicationError;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Application\Common\ServerError;
use Yoyaku\Domain\Customer\CustomerFactory;
use Yoyaku\Domain\Customer\CustomerService;
use Yoyaku\Infrastructure\Repository\Customer\CustomerRepository;

/**
 * 顧客一覧を取得
 */
class CustomersController extends AController
{
    private CustomerService $customer_ds;
    private CustomerRepository $customer_repo;

    public function __construct($customer_ds, $customer_repo)
    {
        $this->customer_ds = $customer_ds;
        $this->customer_repo = $customer_repo;
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_items($request)
    {
        try {
            $params = $request->get_params();
            $users = $this->customer_repo->filter($params);
            $count = $this->customer_repo->filter($params, true);
            $per_page = $request->get_param('per_page');
            return new WP_REST_Response([
                'items' => $users->to_array(),
                'num_pages' => $per_page ? intval(ceil($count / $per_page)) : 1,
                'total' => $count,
            ]);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function add_item($request)
    {
        try {
            $id = $this->customer_ds->add($request->get_params());
            return new WP_REST_Response(['id' => $id]);

        } catch (DuplicationError $e) {
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_item($request)
    {
        try {
            $user = $this->customer_repo->get_by_id($request->get_param('id'));
            return new WP_REST_Response($user->to_array());
        } catch (DataNotFoundException $e) {
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);
        }
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function update_item($request)
    {
        try {
            $id = $request->get_param('id');
            $old_user = $this->customer_repo->get_by_id($id);
            $updated_user = CustomerFactory::create(array_merge($old_user->to_array(), $request->get_params()));

            $email_is_changed = $updated_user->get_email()->get_value() !== $old_user->get_email()->get_value();
            if ($email_is_changed && !$this->customer_ds->can_add($updated_user->get_email())) {
                return new WP_Error(
                    400,
                    __('This email is already in use.', 'yoyaku-manager'),
                    ['status' => 400]
                );
            }

            $this->customer_repo->update_by_entity($id, $updated_user);

            return new WP_REST_Response(['id' => $id]);

        } catch (DataNotFoundException $e) {
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function delete_item($request)
    {
        try {
            $this->customer_repo->delete(['id' => $request->get_param('id')]);
            return new WP_REST_Response(['message' => __('deleted', 'yoyaku-manager')]);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }
}
