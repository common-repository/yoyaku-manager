<?php declare(strict_types=1);

namespace Yoyaku\Application\Payment;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use Yoyaku\Application\Common\AController;
use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Application\Common\ServerError;
use Yoyaku\Domain\EventType\EventBooking\EventBooking;
use Yoyaku\Domain\Payment\PaymentStatus;
use Yoyaku\Infrastructure\Repository\EventType\EventBookingRepository;
use Yoyaku\Infrastructure\WP\DB;

/**
 * Class GetPaymentsController
 */
class PaymentsController extends AController
{
    private EventBookingRepository $event_booking_repo;

    /**
     * @param EventBookingRepository $event_booking_repo
     */
    public function __construct($event_booking_repo)
    {
        $this->event_booking_repo = $event_booking_repo;
    }

    /**
     * 支払いステータスを'返金済み'に変更する
     * Stripe決済の場合は公式サイトから返金処理をする必要がある
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function refund($request)
    {
        try {
            DB::begin();
            /** @var EventBooking $event_booking */
            $event_booking = $this->event_booking_repo->get(
                'transaction_id',
                $request->get_param('transaction_id'),
            );
            $event_booking->set_payment_status(PaymentStatus::REFUNDED);
            $this->event_booking_repo->update_by_entity($event_booking->get_id()->get_value(), $event_booking);
            DB::commit();

            return new WP_REST_Response();

        } catch (DataNotFoundException $e) {
            DB::rollback();
            return new WP_Error($e->getCode(), $e->getMessage(), ['status' => $e->getCode()]);

        } catch (WpDbException $e) {
            DB::rollback();
            return new ServerError($e->getCode(), $e->getMessage());
        }
    }
}
