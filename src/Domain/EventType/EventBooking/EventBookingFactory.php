<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventBooking;

use Exception;
use InvalidArgumentException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\Common\AFactory;
use Yoyaku\Domain\Customer\Birthday;
use Yoyaku\Domain\Customer\CustomerFactory;
use Yoyaku\Domain\Customer\Gender;
use Yoyaku\Domain\Customer\ZipCode;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\EventType\EventTicket\EventTicketFactory;
use Yoyaku\Domain\Payment\GatewayType;
use Yoyaku\Domain\Payment\PaymentData;
use Yoyaku\Domain\Payment\PaymentStatus;
use Yoyaku\Domain\ValueObject\DateTime\DateTimeValue;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\Number\Price;
use Yoyaku\Domain\ValueObject\String\Address;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Domain\ValueObject\String\Phone;
use Yoyaku\Domain\ValueObject\String\Uuid4;

/**
 * Class EventBookingFactory
 */
class EventBookingFactory extends AFactory
{

    /**
     * @param array $fields
     * @return EventBooking
     * @throws InvalidArgumentException|Exception
     */
    public static function create($fields)
    {
        $booking = new EventBooking(
            new Id($fields['customer_id']),
            new Id($fields['event_period_id']),
            BookingStatus::tryFrom($fields['status']),
            new Email($fields['email']),
            new Name(trim($fields['first_name'])),
            new Name(trim($fields['last_name'])),
            new Price(floatval($fields['amount'])),
            GatewayType::tryFrom($fields['gateway']),
        );

        if (isset($fields['id'])) {
            $booking->set_id(new Id($fields['id']));
        }

        if (isset($fields['phone'])) {
            $booking->set_phone(new Phone($fields['phone']));
        }

        if (!empty($fields['first_name_ruby'])) {
            $booking->set_first_name_ruby(new Name($fields['first_name_ruby']));
        }

        if (!empty($fields['last_name_ruby'])) {
            $booking->set_last_name_ruby(new Name($fields['last_name_ruby']));
        }

        if (!empty($fields['zipcode'])) {
            $booking->set_zipcode(new ZipCode($fields['zipcode']));
        }

        if (!empty($fields['address'])) {
            $booking->set_address(new Address($fields['address']));
        }

        if (!empty($fields['birthday'])) {
            $booking->set_birthday(new Birthday($fields['birthday']));
        }

        if (!empty($fields['gender'])) {
            $booking->set_gender(Gender::tryFrom($fields['gender']));
        }

        if (isset($fields['memo'])) {
            $booking->set_memo(new Memo($fields['memo']));
        }

        if (!empty($fields['transaction_id'])) {
            $booking->set_transaction_id(new Name($fields['transaction_id']));
        }

        if (isset($fields['token'])) {
            $booking->set_token(new Uuid4($fields['token']));
        }

        if (!empty($fields['created'])) {
            $booking->set_created(
                new DateTimeValue(DateTimeService::get_custom_datetime_object_from_utc($fields['created']))
            );
        }

        if (isset($fields['payment_status'])) {
            $booking->set_payment_status(PaymentStatus::tryFrom($fields['payment_status']));
        }

        if (isset($fields['customer'])) {
            $booking->set_customer(CustomerFactory::create($fields['customer']));
        }

        if (isset($fields['ep_start_datetime'])) {
            $booking->set_period_start_datetime(
                new DateTimeValue(DateTimeService::get_custom_datetime_object_from_utc($fields['ep_start_datetime']))
            );
        }

        if (isset($fields['event_name'])) {
            $booking->set_event_name(new Name($fields['event_name']));
        }

        $tickets = new Collection();
        if (isset($fields['tickets'])) {
            foreach ($fields['tickets'] as $key => $value) {
                if (isset($value['event_id']) && isset($value['ticket_count']) && isset($value['price'])) {
                    $tickets->add_item(EventTicketFactory::create($value), $key);
                }
            }
        }
        $booking->set_tickets($tickets);

        return $booking;
    }

}
