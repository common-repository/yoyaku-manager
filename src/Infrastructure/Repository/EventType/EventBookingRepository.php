<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Repository\EventType;

use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\EventType\EventBooking\EventBookingFactory;
use Yoyaku\Infrastructure\Repository\ARepository;
use Yoyaku\Infrastructure\Tables\Event\EventBookingsTable;
use Yoyaku\Infrastructure\Tables\Event\EventPeriodsTable;
use Yoyaku\Infrastructure\Tables\Event\EventsTable;

class EventBookingRepository extends ARepository
{
    const FACTORY = EventBookingFactory::class;

    public function __construct()
    {
        $table = EventBookingsTable::get_table_name();
        parent::__construct($table);
    }

    /**
     * @param int $id
     * @return Collection|int
     * @throws DataNotFoundException
     */
    public function get_by_id($id)
    {
        global $wpdb;

        $periods_table = EventPeriodsTable::get_table_name();
        $events_table = EventsTable::get_table_name();
        $query = $wpdb->prepare("
            SELECT 
                eb.id AS id,
                eb.customer_id AS customer_id,
                eb.event_period_id AS event_period_id,
                eb.email AS email,
                eb.first_name AS first_name,
                eb.last_name AS last_name,
                eb.first_name_ruby AS first_name_ruby,
                eb.last_name_ruby AS last_name_ruby,
                eb.phone AS phone,
                eb.birthday AS birthday,
                eb.address AS address,
                eb.zipcode AS zipcode,
                eb.gender AS gender,
                eb.status AS status,
                eb.memo AS memo,
                eb.amount AS amount,
                eb.payment_status AS payment_status,
                eb.gateway AS gateway,
                eb.transaction_id AS transaction_id,
                eb.token AS token,
                eb.created AS created,

                ep.start_datetime AS ep_start_datetime,
                
                e.name AS event_name
            FROM $this->table eb
                LEFT JOIN $periods_table ep ON eb.event_period_id = ep.id
                LEFT JOIN $events_table e ON ep.event_id = e.id
            WHERE eb.id=%d",
            $id,
        );
        $row = $wpdb->get_row($query, ARRAY_A);
        if (!$row) {
            throw new DataNotFoundException();
        }

        return call_user_func([static::FACTORY, 'create'], $row);
    }

    /**
     * @param array $filter
     * @return Collection|int
     * @throws WpDbException
     */
    public function filter($filter = [], $count = false)
    {
        global $wpdb;

        $periods_table = EventPeriodsTable::get_table_name();
        $events_table = EventsTable::get_table_name();
        $where = '';
        if (!empty($filter['event_period_id'])) {
            $where .= $wpdb->prepare(' AND eb.event_period_id = %d', $filter['event_period_id']);
        }

        if (!empty($filter['event_id'])) {
            $where .= $wpdb->prepare(' AND ep.event_id = %d', $filter['event_id']);
        }

        if (!empty($filter['search'])) {
            $where .= $this->_get_search_sql($filter['search'], ['e.name', 'ep.location']);
        }

        if (!empty($filter['payment_status'])) {
            $where .= $wpdb->prepare(' AND eb.payment_status = %s', $filter['payment_status']);
        }

        if (!empty($filter['status'])) {
            $where .= $wpdb->prepare(' AND eb.status = %s', $filter['status']);
        }

        $orderby = 'ORDER BY id DESC';

        $limit = '';
        if (isset($filter['page'], $filter['per_page'])) {
            $limit = $this->get_limit(absint($filter['page']), absint($filter['per_page']));
        }

        if ($count) {
            return intval($wpdb->get_var("
                SELECT COUNT(*) 
                FROM $this->table eb
                    LEFT JOIN $periods_table ep ON eb.event_period_id = ep.id
                WHERE 1=1 $where"
            ));
        } else {
            $rows = $wpdb->get_results(
                "SELECT 
                    eb.id AS id,
                    eb.customer_id AS customer_id,
                    eb.event_period_id AS event_period_id,
                    eb.email AS email,
                    eb.first_name AS first_name,
                    eb.last_name AS last_name,
                    eb.first_name_ruby AS first_name_ruby,
                    eb.last_name_ruby AS last_name_ruby,
                    eb.phone AS phone,
                    eb.birthday AS birthday,
                    eb.address AS address,
                    eb.zipcode AS zipcode,
                    eb.gender AS gender,
                    eb.status AS status,
                    eb.memo AS memo,
                    eb.amount AS amount,
                    eb.payment_status AS payment_status,
                    eb.gateway AS gateway,
                    eb.transaction_id AS transaction_id,
                    eb.token AS token,
                    eb.created AS created,
    
                    ep.start_datetime AS ep_start_datetime,
                    
                    e.name AS event_name
                FROM $this->table eb
                    LEFT JOIN $periods_table ep ON eb.event_period_id = ep.id
                    LEFT JOIN $events_table e ON ep.event_id = e.id
                WHERE 1=1 {$where}
                {$orderby}
                $limit",
                ARRAY_A,
            );
            return call_user_func([static::FACTORY, 'create_collection'], $rows);
        }
    }

    /**
     * 複合ユニーク制約のデータの存在確認
     * @param int $customer_id
     * @param int $period_id
     * @return bool
     */
    public function is_exist($customer_id, $period_id)
    {
        global $wpdb;

        return !!$wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM $this->table WHERE customer_id = %d AND event_period_id = %d",
                $customer_id,
                $period_id,
            )
        );
    }
}
