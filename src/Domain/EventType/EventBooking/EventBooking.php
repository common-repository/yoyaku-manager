<?php declare(strict_types=1);

namespace Yoyaku\Domain\EventType\EventBooking;

use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\Customer\Birthday;
use Yoyaku\Domain\Customer\Customer;
use Yoyaku\Domain\Customer\Gender;
use Yoyaku\Domain\Customer\ZipCode;
use Yoyaku\Domain\Payment\GatewayType;
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
 * イベント予約 entity
 */
class EventBooking
{
    private ?Id $id = null;
    /**
     * @var Id 予約した顧客のid
     */
    private Id $customer_id;
    private Id $event_period_id;
    /**
     * @var Email 予約時のメール
     */
    private Email $email;
    /**
     * @var Name 予約時の性
     */
    private Name $first_name;
    /**
     * @var Name 予約時の名
     */
    private Name $last_name;
    private Name $first_name_ruby;
    private Name $last_name_ruby;
    /**
     * @var Phone 予約時の電話番号
     */
    private Phone $phone;
    private BookingStatus $status;
    private ZipCode $zipcode;
    private Address $address;
    private ?Birthday $birthday = null;
    private Gender $gender;
    private Memo $memo;
    private ?DateTimeValue $created = null;

    // --- 支払い関連データ ---
    private Name $transaction_id;
    /**
     * @var Price 支払い金額
     */
    private Price $amount;
    private PaymentStatus $payment_status;
    private GatewayType $gateway;
    private Uuid4 $token;

    // --- 外部参照データ ---
    /**
     * @var Customer|null 予約した顧客のデータ
     */
    private ?Customer $customer = null;
    /**
     * @var Collection 購入したチケットのリスト
     */
    private Collection $tickets;

    /**
     * イベント期間の開始日時 表示用
     * @var DateTimeValue|null
     */
    private ?DateTimeValue $period_start_datetime = null;
    /**
     * イベント名 表示用
     * @var Name
     */
    private Name $event_name;

    /**
     * @param Id $customer_id
     * @param Id $event_period_id
     * @param BookingStatus $status
     * @param Email $email
     * @param Name $first_name
     * @param Name $last_name
     * @param Price $amount
     * @param GatewayType $gateway
     */
    public function __construct(
        Id            $customer_id,
        Id            $event_period_id,
        BookingStatus $status,
        Email         $email,
        Name          $first_name,
        Name          $last_name,
        Price         $amount,
        GatewayType   $gateway,
    )
    {
        $this->customer_id = $customer_id;
        $this->event_period_id = $event_period_id;
        $this->status = $status;
        $this->email = $email;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->amount = $amount;
        $this->gateway = $gateway;

        $this->phone = new Phone();
        $this->first_name_ruby = new Name();
        $this->last_name_ruby = new Name();
        $this->zipcode = new ZipCode('');
        $this->address = new Address('');
        $this->gender = Gender::UNKNOWN;
        $this->memo = new Memo();
        $this->transaction_id = new Name();
        $this->payment_status = PaymentStatus::PENDING;
        $this->token = new Uuid4();

        $this->tickets = new Collection();
        $this->event_name = new Name();
    }

    /**
     * @return Id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @param Id $id
     */
    public function set_id(Id $id)
    {
        $this->id = $id;
    }

    /**
     * @return Uuid4
     */
    public function get_token()
    {
        return $this->token;
    }

    /**
     * @param Uuid4 $token
     */
    public function set_token(Uuid4 $token)
    {
        $this->token = $token;
    }

    /**
     * @return Id
     */
    public function get_customer_id()
    {
        return $this->customer_id;
    }

    /**
     * @return Id
     */
    public function get_event_period_id()
    {
        return $this->event_period_id;
    }

    /**
     * @return BookingStatus
     */
    public function get_status()
    {
        return $this->status;
    }

    /**
     * @param BookingStatus $status
     */
    public function set_status(BookingStatus $status)
    {
        return $this->status = $status;
    }

    /**
     * @return Email
     */
    public function get_email()
    {
        return $this->email;
    }

    /**
     * @param Email $email
     */
    public function set_email(Email $email)
    {
        $this->email = $email;
    }

    /**
     * @return Name
     */
    public function get_first_name()
    {
        return $this->first_name;
    }

    /**
     * @param Name $first_name
     */
    public function set_first_name(Name $first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return Name
     */
    public function get_last_name()
    {
        return $this->last_name;
    }

    /**
     * @param Name $last_name
     */
    public function set_last_name(Name $last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @return Name
     */
    public function get_first_name_ruby()
    {
        return $this->first_name_ruby;
    }

    /**
     * @param Name $first_name_ruby
     */
    public function set_first_name_ruby(Name $first_name_ruby)
    {
        $this->first_name_ruby = $first_name_ruby;
    }

    /**
     * @return Name
     */
    public function get_last_name_ruby()
    {
        return $this->last_name_ruby;
    }

    /**
     * @param Name $last_name_ruby
     */
    public function set_last_name_ruby(Name $last_name_ruby)
    {
        $this->last_name_ruby = $last_name_ruby;
    }

    /**
     * @return Phone
     */
    public function get_phone()
    {
        return $this->phone;
    }

    /**
     * @param Phone $phone
     */
    public function set_phone(Phone $phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return ZipCode
     */
    public function get_zipcode()
    {
        return $this->zipcode;
    }

    /**
     * @param ZipCode $zipcode
     */
    public function set_zipcode($zipcode)
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @return Address
     */
    public function get_address()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function set_address($address)
    {
        $this->address = $address;
    }

    /**
     * @return Birthday
     */
    public function get_birthday()
    {
        return $this->birthday;
    }

    /**
     * @param Birthday|null $birthday
     */
    public function set_birthday(?Birthday $birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return Gender
     */
    public function get_gender()
    {
        return $this->gender;
    }

    /**
     * @param Gender $gender
     */
    public function set_gender(Gender $gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return Memo
     */
    public function get_memo()
    {
        return $this->memo;
    }

    /**
     * @param Memo $memo
     */
    public function set_memo(Memo $memo)
    {
        $this->memo = $memo;
    }

    /**
     * @return Price
     */
    public function get_amount()
    {
        return $this->amount;
    }

    /**
     * @param Price $amount
     */
    public function set_amount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return PaymentStatus
     */
    public function get_payment_status()
    {
        return $this->payment_status;
    }

    /**
     * @param PaymentStatus $payment_status
     */
    public function set_payment_status($payment_status)
    {
        $this->payment_status = $payment_status;
    }

    /**
     * @return GatewayType
     */
    public function get_gateway()
    {
        return $this->gateway;
    }

    /**
     * @param GatewayType $gateway
     */
    public function set_gateway($gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return Name
     */
    public function get_transaction_id()
    {
        return $this->transaction_id;
    }

    /**
     * @param Name $transaction_id
     */
    public function set_transaction_id(Name $transaction_id)
    {
        $this->transaction_id = $transaction_id;
    }

    /**
     * @return DateTimeValue
     */
    public function get_created()
    {
        return $this->created;
    }

    /**
     * @param DateTimeValue|null $created
     */
    public function set_created($created)
    {
        $this->created = $created;
    }

    /**
     * @return Collection
     */
    public function get_tickets()
    {
        return $this->tickets;
    }

    /**
     * @param Collection $tickets
     */
    public function set_tickets(Collection $tickets)
    {
        $this->tickets = $tickets;
    }

    /**
     * @return Customer
     */
    public function get_customer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function set_customer(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return DateTimeValue
     */
    public function get_period_start_datetime()
    {
        return $this->period_start_datetime;
    }

    /**
     * @param DateTimeValue $period_start_datetime
     */
    public function set_period_start_datetime($period_start_datetime)
    {
        $this->period_start_datetime = $period_start_datetime;
    }

    /**
     * @return Name
     */
    public function get_event_name()
    {
        return $this->event_name;
    }

    /**
     * @param Name $event_name
     */
    public function set_event_name($event_name)
    {
        $this->event_name = $event_name;
    }

    /**
     * @return array
     */
    public function to_table_data()
    {
        $result = $this->to_array();
        unset(
            $result['id'],
            $result['customer'],
            $result['tickets'],
            $result['period_start_datetime'],
            $result['event_name'],
        );

        return $result;
    }

    /**
     * @return array
     */
    public function to_array()
    {
        return [
            'id' => $this->get_id()?->get_value(),
            'token' => $this->get_token()->get_value(),
            'customer_id' => $this->get_customer_id()->get_value(),
            'event_period_id' => $this->get_event_period_id()->get_value(),
            'status' => $this->get_status()->value,
            'email' => $this->get_email()->get_value(),
            'first_name' => $this->get_first_name()->get_value(),
            'last_name' => $this->get_last_name()->get_value(),
            'first_name_ruby' => $this->get_first_name_ruby()->get_value(),
            'last_name_ruby' => $this->get_last_name_ruby()->get_value(),
            'phone' => $this->get_phone()->get_value(),
            'zipcode' => $this->get_zipcode()->get_value(),
            'address' => $this->get_address()->get_value(),
            'birthday' => $this->get_birthday()?->get_value(),
            'gender' => $this->get_gender()->value,
            'memo' => $this->get_memo()->get_value(),
            'customer' => $this->get_customer()?->to_array(),

            'transaction_id' => $this->get_transaction_id()->get_value(),
            'amount' => $this->get_amount()->get_value(),
            'gateway' => $this->get_gateway()->value,
            'payment_status' => $this->get_payment_status()->value,
            'created' => $this->get_created()?->get_format_value(),

            'tickets' => $this->get_tickets()->to_array(),
            'period_start_datetime' => $this->get_period_start_datetime()?->get_format_value(),
            'event_name' => $this->get_event_name()->get_value(),
        ];
    }

    /**
     * エクスポート用の予約データを取得
     * @return array
     */
    public function to_array_to_export()
    {
        return [
            'ID' => $this->get_id()?->get_value() ?: "",
            __('Event Name', 'yoyaku-manager') => $this->get_event_name()->get_value(),
            __('Start DateTime', 'yoyaku-manager') => $this->get_period_start_datetime()?->get_format_value(),
            __('Customer ID', 'yoyaku-manager') => $this->get_customer_id()->get_value(),
            __('Period ID', 'yoyaku-manager') => $this->get_event_period_id()->get_value(),
            __('Booking Status', 'yoyaku-manager') => $this->get_status()->label(),
            __('Email', 'yoyaku-manager') => $this->get_email()->get_value(),
            __('First Name', 'yoyaku-manager') => $this->get_first_name()->get_value(),
            __('Last Name', 'yoyaku-manager') => $this->get_last_name()->get_value(),
            __('First Name Ruby', 'yoyaku-manager') => $this->get_first_name_ruby()->get_value(),
            __('Last Name Ruby', 'yoyaku-manager') => $this->get_last_name_ruby()->get_value(),
            __('Phone', 'yoyaku-manager') => $this->get_phone()->get_value(),
            __('Birthday', 'yoyaku-manager') => $this->get_birthday()?->get_value() ?: "",
            __('Gender', 'yoyaku-manager') => $this->get_gender()->value,
            __('Zipcode', 'yoyaku-manager') => $this->get_zipcode()->get_value(),
            __('Address', 'yoyaku-manager') => $this->get_address()->get_value(),
            __('Memo', 'yoyaku-manager') => $this->get_memo()->get_value(),
            __('Payment Status', 'yoyaku-manager') => $this->get_payment_status()->label(),
            __('Gateway', 'yoyaku-manager') => $this->get_gateway()->label(),
            __('Payment Amount', 'yoyaku-manager') => $this->get_amount()->get_value(),
            __('Transaction ID', 'yoyaku-manager') => $this->get_transaction_id()->get_value(),
            __('Created', 'yoyaku-manager') => $this->get_created()?->get_format_value(),
        ];
    }
}
