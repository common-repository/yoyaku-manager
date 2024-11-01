<?php declare(strict_types=1);

namespace Yoyaku\Application\EventType\Event;

use Exception;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Yoyaku\Application\Common\AController;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Application\Common\ServerError;
use Yoyaku\Application\Notification\NotificationQuery;
use Yoyaku\Domain\EventType\Event\EventService;
use Yoyaku\Domain\Notification\NotificationEventService;
use Yoyaku\Infrastructure\Repository\EventType\EventRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventTicketRepository;
use Yoyaku\Infrastructure\Repository\Notification\NotificationEventRepository;
use Yoyaku\Infrastructure\WP\DB;

/**
 * イベント一覧取得 並び順はidの降順
 */
class EventsController extends AController
{
    private EventApplicationService $event_as;
    private NotificationQuery $notification_query;
    private EventService $event_ds;
    private NotificationEventService $notification_event_ds;
    private EventRepository $event_repo;
    private EventTicketRepository $ticket_repo;
    private NotificationEventRepository $notification_event_repo;

    public function __construct(
        $event_as,
        $notification_query,
        $event_ds,
        $notification_event_ds,
        $event_repo,
        $ticket_repo,
        $notification_event_repo,
    )
    {
        $this->event_as = $event_as;
        $this->notification_query = $notification_query;
        $this->event_ds = $event_ds;
        $this->notification_event_ds = $notification_event_ds;
        $this->event_repo = $event_repo;
        $this->ticket_repo = $ticket_repo;
        $this->notification_event_repo = $notification_event_repo;
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_items($request)
    {
        try {
            $params = $request->get_params();
            $events = $this->event_repo->filter($params);
            $count = $this->event_repo->filter($params, true);
            $per_page = $request->get_param('per_page');
            return new WP_REST_Response([
                'items' => $events->to_array(),
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
        $params = $request->get_params();
        try {
            DB::begin();

            $id = $this->event_ds->add($params);
            $this->notification_event_ds->bulk_add($id, $request->get_param('notification_ids'));

            DB::commit();
            return new WP_REST_Response(['id' => $id]);

        } catch (WpDbException $e) {
            DB::rollback();
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     * @throws WpDbException
     */
    public function get_item($request)
    {
        try {
            $id = $request->get_param('id');
            $event = $this->event_repo->get_by_id($id)->to_array();
            $event['tickets'] = $this->ticket_repo->filter(['event_id' => $id])->to_array();
            $notifications = $this->notification_query->filter_by_notification_event(['event_id' => $id]);
            $event['notifications'] = $notifications->to_array();

            return new WP_REST_Response($event);

        } catch (DataNotFoundException $e) {
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);
        }
    }

    /**
     * イベント、通知を更新する
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     * @throws Exception
     */
    public function update_item($request)
    {
        try {
            DB::begin();
            $id = $request->get_param('id');
            $this->event_as->update($id, $request->get_params());

            // 通知データは洗い替える
            $notification_ids = $request->get_param('notification_ids');
            if ($notification_ids) {
                $this->notification_event_repo->delete(['event_id' => $id]);
                $this->notification_event_ds->bulk_add($id, $notification_ids);
            }

            DB::commit();
            return new WP_REST_Response(['id' => $id]);

        } catch (DataNotFoundException $e) {
            DB::rollback();
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);

        } catch (WpDbException $e) {
            DB::rollback();
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * イベントとイベントに関連するデータを削除
     * イベント期間、イベント予約、通知ログ、イベント通知はイベント削除時に、外部キー制約により削除される
     * 外部キー制約により削除される
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function delete_item($request)
    {
        try {
            $this->event_repo->delete(['id' => $request->get_param('id')]);
            return new WP_REST_Response(['message' => 'success']);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_calendar_items($request)
    {
        try {
            $params = $request->get_params();
            $events = $this->event_repo->filter_for_calendar($params);
            return new WP_REST_Response($events);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }
}
