<?php declare(strict_types=1);

namespace Yoyaku\Application\Worker;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Yoyaku\Application\Common\AController;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Application\Common\ServerError;
use Yoyaku\Domain\Worker\WorkerService;
use Yoyaku\Infrastructure\Repository\Worker\WorkerRepository;
use Yoyaku\Infrastructure\Repository\Worker\WPUserRepository;

/**
 * ワーカー一覧取得
 */
class WorkersController extends AController
{
    private WorkerService $worker_ds;
    private WorkerRepository $worker_repo;
    private WPUserRepository $wp_user_repo;

    /**
     * @param WorkerService $worker_ds
     * @param WorkerRepository $worker_repo
     * @param WPUserRepository $wp_user_repo
     */
    public function __construct($worker_ds, $worker_repo, $wp_user_repo)
    {
        $this->worker_ds = $worker_ds;
        $this->worker_repo = $worker_repo;
        $this->wp_user_repo = $wp_user_repo;

    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_items($request)
    {
        try {
            $params = $request->get_params();
            $count = $this->wp_user_repo->filter($params, true);
            $wp_users = $this->wp_user_repo->filter($params);
            $ids = [];
            foreach ($wp_users->get_items() as $user) {
                $ids[] = $user->get_id()->get_value();
            }

            $workers = $this->worker_repo->filter_by_ids($ids);
            foreach ($wp_users->get_items() as $user) {
                $id = $user->get_id()->get_value();
                if ($workers->key_exists($id)) {
                    $user->set_worker($workers->get_item($id));
                }
            }

            $per_page = $request->get_param('per_page');
            return new WP_REST_Response([
                'items' => $wp_users->to_array(),
                'num_pages' => $per_page ? intval(ceil($count / $per_page)) : 1,
                'total' => $count,
            ]);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * wpユーザーと連携しているWorker取得。データがない場合は作成する
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_item($request)
    {
        $id = $request->get_param('id');
        try {
            $wp_user = $this->wp_user_repo->get_by_id($id);
        } catch (DataNotFoundException $e) {
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);
        }

        try {
            $worker = $this->worker_repo->get_by_id($id);
            $wp_user->set_worker($worker);
        } catch (DataNotFoundException) {
            try {
                $this->worker_ds->add(['id' => $id]);
            } catch (WpDbException $e) {
                return new ServerError($e->getCode(), $e->getMessage());
            }
        }

        return new WP_REST_Response($wp_user->to_array());
    }

    /**
     * workerデータを更新する
     * 取得権限があるユーザーのみが更新可能
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function update_item($request)
    {
        try {
            $id = $request->get_param('id');
            // 権限チェックのために実行
            $this->wp_user_repo->get_by_id($id);
            $this->worker_ds->update($id, $request->get_params());
            return new WP_REST_Response(['message' => 'success']);

        } catch (DataNotFoundException $e) {
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }
}
