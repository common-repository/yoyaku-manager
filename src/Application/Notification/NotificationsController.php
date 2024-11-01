<?php declare(strict_types=1);

namespace Yoyaku\Application\Notification;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Yoyaku\Application\Common\AController;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\NotAllowedError;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Application\Common\ServerError;
use Yoyaku\Domain\Notification\NotificationService;
use Yoyaku\Domain\Notification\NotificationTiming;
use Yoyaku\Infrastructure\Repository\Notification\NotificationRepository;

/**
 * 通知一覧を取得
 */
class NotificationsController extends AController
{
    private EmailApplicationService $email_as;
    private NotificationService $notification_ds;
    private NotificationRepository $notification_repo;

    public function __construct($email_as, $notification_ds, $notification_repo)
    {
        $this->email_as = $email_as;
        $this->notification_ds = $notification_ds;
        $this->notification_repo = $notification_repo;
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_items($request)
    {
        try {
            $notifications = $this->notification_repo->filter($request->get_params());
            return new WP_REST_Response(['items' => $notifications->to_array()]);

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
            $notification = $this->notification_repo->get_by_id($request->get_param('id'));
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
    public function update_item($request)
    {
        try {
            $params = $request->get_params();
            // 定期通知の場合は、必要なパラメーターの存在チェック
            if (isset($params['timing'])
                && $params['timing'] === NotificationTiming::SCHEDULED->value
                && !isset($params['days'], $params['time'], $params['is_before'])
            ) {
                return new WP_Error('400', 'invalid parameter(s)', ['status' => 400]);
            }

            $this->notification_ds->update($request->get_param('id'), $params);
            return new WP_REST_Response(['message' => 'success']);

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
    public function send_scheduled_mails($request)
    {
        try {
            $this->email_as->send_scheduled_mails();
            return new WP_REST_Response();

        } catch (NotAllowedError $e) {
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);

        } catch (WpDbException|\PHPMailer\PHPMailer\Exception $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }
}
