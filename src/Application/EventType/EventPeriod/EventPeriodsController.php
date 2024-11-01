<?php declare(strict_types=1);

namespace Yoyaku\Application\EventType\EventPeriod;

use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Yoyaku\Application\Common\AController;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Application\Common\ServerError;
use Yoyaku\Application\Meeting\IMeetingApplicationService;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\EventType\EventPeriod\EventPeriod;
use Yoyaku\Domain\EventType\EventPeriod\EventPeriodFactory;
use Yoyaku\Domain\EventType\EventPeriod\EventPeriodService;
use Yoyaku\Domain\Setting\SettingsService;
use Yoyaku\Infrastructure\Repository\EventType\EventPeriodRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventRepository;
use Yoyaku\Infrastructure\Repository\EventType\EventTicketRepository;
use Yoyaku\Infrastructure\WP\DB;

class EventPeriodsController extends AController
{
    private IMeetingApplicationService $meeting_as;
    private EventPeriodService $event_period_ds;
    private EventRepository $event_repo;
    private EventPeriodRepository $period_repo;
    private EventTicketRepository $ticket_repo;

    public function __construct($meeting_as, $event_period_ds, $event_repo, $event_period_repo, $ticket_repo)
    {
        $this->meeting_as = $meeting_as;
        $this->event_period_ds = $event_period_ds;
        $this->event_repo = $event_repo;
        $this->period_repo = $event_period_repo;
        $this->ticket_repo = $ticket_repo;
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_items($request)
    {
        try {
            $params = $request->get_params();
            if (!$request->get_param('show_past')) {
                $params['start_datetime'] = DateTimeService::get_now_date() . ' 00:00:00';
            }
            $event_periods = $this->period_repo->filter($params);
            $count = $this->period_repo->filter($params, true);
            $per_page = $request->get_param('per_page');
            return new WP_REST_Response([
                'items' => $event_periods->to_array(),
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
     * @throws Exception DateTimeの引数が不正の場合
     */
    public function add_item($request)
    {
        $settings = SettingsService::get_instance();
        $params = $request->get_params();
        try {
            if (!$this->event_repo->exists($request->get_param('event_id'))) {
                return new WP_Error(404, 'event_not_found', ['status' => 404]);
            }

            $period_id = $this->event_period_ds->add($params);

            $period = EventPeriodFactory::create($params);
            $now = new DateTimeImmutable();
            if (($settings->google_is_active() || $settings->zoom_is_active())
                && $now < $period->get_start_datetime()->get_value()
            ) {
                $this->meeting_as->add_meeting($period_id);
            }

            return new WP_REST_Response(['id' => $period_id]);

        } catch (InvalidArgumentException $e) {
            // 終了日時 < 開始日時 の場合発生する
            return new WP_Error(400, $e->getMessage(), ['status' => 400]);

        } catch (WpDbException|Exception $e) {
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
            $result = [];
            if ($request->has_param('id')) {
                $id = $request->get_param('id');
                /** @var EventPeriod $period */
                $period = $this->period_repo->get_by_id($id);
                $result = $period->to_array();
                $sold_tickets = $this->ticket_repo->filter(['event_period_id' => $id, 'with_sold_count' => true]);
                $result['tickets'] = $sold_tickets->to_array();

            } else if ($request->has_param('uuid')) {
                $period = $this->period_repo->get_by_uuid($request->get_param('uuid'));
                $result = $period->to_array();
            }

            return new WP_REST_Response($result);

        } catch (DataNotFoundException $e) {
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     * @throws Exception
     */
    public function update_item($request)
    {
        // 開始日時　< 終了日時のチェック
        $start_datetime = $request->get_param('start_datetime');
        $end_datetime = $request->get_param('end_datetime');
        $start_dt = $start_datetime ? new DateTimeImmutable($start_datetime) : null;
        $end_dt = $end_datetime ? new DateTimeImmutable($end_datetime) : null;
        if (($start_dt && is_null($end_dt)) || (is_null($start_dt) && $end_dt)) {
            // 日時を変更したい場合は、開始日時と終了日時の両方指定しなければいけない仕様
            return new WP_Error(
                400,
                'Invalid parameter(s): start_datetime, end_datetime.',
                ['status' => 400]
            );
        }

        $settings = SettingsService::get_instance();
        try {
            DB::begin();
            $id = $request->get_param('id');
            $wp_id = $request->get_param('wp_id');
            /** @var EventPeriod $old_period */
            $old_period = $this->period_repo->get_by_id($id);
            $new_period = EventPeriodFactory::create(array_merge($old_period->to_array(), $request->get_params()));
            $this->period_repo->update_by_entity($id, $new_period);
            $this->event_period_ds->update($id, $request->get_params());


            $now = new DateTimeImmutable();
            $old_period_wp_id = $old_period->get_wp_id()?->get_value();
            $new_period_wp_id = $new_period->get_wp_id()?->get_value();
            if (($settings->google_is_active() || $settings->zoom_is_active())
                && $now < $new_period->get_start_datetime()->get_value()

            ) {
                if (is_null($old_period_wp_id) && !is_null($new_period_wp_id)) {
                    // 担当者無し → 担当者ありに変更
                    $this->meeting_as->add_meeting($id);

                } else if ((!is_null($new_period_wp_id) && $new_period_wp_id === $old_period_wp_id)
                    && (!is_null($start_dt) && $start_dt != $old_period->get_start_datetime()->get_value())
                    || (!is_null($end_dt) && $end_dt != $old_period->get_end_datetime()->get_value())
                ) {
                    // 担当者の変更なし && (開始日時 or 終了日時の変更)
                    $this->meeting_as->update_meeting($id);
                }
            }


            DB::commit();
            return new WP_REST_Response(['id' => $id]);

        } catch (InvalidArgumentException $e) {
            // 終了日時 < 開始日時 の場合発生する
            DB::rollback();
            return new WP_Error(400, $e->getMessage(), ['status' => 400]);

        } catch (DataNotFoundException $e) {
            DB::rollback();
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);

        } catch (WpDbException $e) {
            DB::rollback();
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * 外部キー制約によりEventBookingも削除される
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function delete_item($request)
    {
        try {
            $this->period_repo->delete(['id' => $request->get_param('id')]);
            return new WP_REST_Response(['message' => 'success']);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }
}
