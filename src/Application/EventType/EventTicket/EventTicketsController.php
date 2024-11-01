<?php declare(strict_types=1);

namespace Yoyaku\Application\EventType\EventTicket;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Yoyaku\Application\Common\AController;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Application\Common\ServerError;
use Yoyaku\Domain\EventType\EventTicket\EventTicketService;
use Yoyaku\Infrastructure\Repository\EventType\EventTicketRepository;

class EventTicketsController extends AController
{
    private EventTicketService $ticket_ds;
    private EventTicketRepository $ticket_repo;

    public function __construct($ticket_ds, $ticket_repo)
    {
        $this->ticket_ds = $ticket_ds;
        $this->ticket_repo = $ticket_repo;
    }

    /**
     * with_sold_countの指定あり → 予約の編集時に使う
     * with_sold_countの指定なし → イベント詳細のチケット一覧を表示する時などに使う
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_items($request)
    {
        try {
            $tickets = $this->ticket_repo->filter($request->get_params());
            return new WP_REST_Response(['items' => $tickets->to_array()]);

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
            $ticket = $this->ticket_repo->get_by_id($request->get_param('id'));
            return new WP_REST_Response($ticket->to_array());

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
    public function add_item($request)
    {
        try {
            $id = $this->ticket_ds->add($request->get_params());
            return new WP_REST_Response(['id' => $id]);

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
            $this->ticket_ds->update($request->get_param('id'), $request->get_params());
            return new WP_REST_Response(['message' => 'success']);

        } catch (DataNotFoundException $e) {
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * ticketのデータを削除。
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function delete_item($request)
    {
        try {
            $this->ticket_repo->delete(['id' => $request->get_param('id')]);
            return new WP_REST_Response(['message' => 'success']);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * block, 管理画面兼用 特定のイベント期間のチケットの販売データを含めたチケット情報
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_items_with_sold_count_for_front($request)
    {
        try {
            $params = $request->get_params();
            $params['with_sold_count'] = true;
            $tickets = $this->ticket_repo->filter($params);
            return new WP_REST_Response(['items' => $tickets->to_array_for_customer()]);

        } catch (WpDbException $e) {
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }
}
