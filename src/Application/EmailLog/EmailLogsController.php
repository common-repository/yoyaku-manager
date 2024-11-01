<?php declare(strict_types=1);

namespace Yoyaku\Application\EmailLog;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Yoyaku\Application\Common\AController;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Application\Common\ServerError;
use Yoyaku\Infrastructure\Repository\Notification\EmailLogRepository;

/**
 * 通知一覧を取得
 */
class EmailLogsController extends AController
{
    private EmailLogApplicationService $email_log_as;
    private EmailLogRepository $email_log_repo;

    public function __construct($email_as, $email_log_repo)
    {
        $this->email_log_as = $email_as;
        $this->email_log_repo = $email_log_repo;
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_items($request)
    {
        try {
            $params = $request->get_params();
            $notifications = $this->email_log_repo->filter($params);
            $filtered_count = $this->email_log_repo->filter($params, true);
            $total_count = $this->email_log_repo->filter([], true);
            $failed_count = $this->email_log_repo->filter(['sent' => false], true);
            $per_page = $request->get_param('per_page');
            return new WP_REST_Response([
                'items' => $notifications->to_array(),
                'num_pages' => $per_page ? intval(ceil($filtered_count / $per_page)) : 1,
                'all_count' => $total_count,
                'failed_count' => $failed_count,
            ]);

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
            $notification = $this->email_log_repo->get_by_id($request->get_param('id'));
            return new WP_REST_Response($notification->to_array());

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
            $this->email_log_repo->delete(['id' => $request->get_param('id')]);
            return new WP_REST_Response(['message' => 'success']);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 送信に失敗したメールを再送する
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function send_undelivered_emails($request)
    {
        try {
            $filter = [];
            if ($request->get_param('id')) {
                $filter['id'] = $request->get_param('id');
            }
            $this->email_log_as->send_undelivered_notifications($filter);
            return new WP_REST_Response(['message' => 'success']);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }
}
