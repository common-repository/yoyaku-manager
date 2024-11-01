<?php declare(strict_types=1);

namespace Yoyaku\Domain\Customer;

use Yoyaku\Domain\ValueObject\DateTime\DateTimeValue;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Address;
use Yoyaku\Domain\ValueObject\String\Description;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Domain\ValueObject\String\Phone;

/**
 * Class Customer Entity
 */
class Customer
{
    /** @var Id|null */
    private ?Id $id = null;
    private ?Id $wp_id = null;
    private Name $first_name;
    private Name $last_name;
    private Email $email;
    private Phone $phone;
    private Name $first_name_ruby;
    private Name $last_name_ruby;
    private Gender $gender;
    private ZipCode $zipcode;
    private Address $address;
    private ?Birthday $birthday = null;
    /**
     * @var Description 管理用メモ。管理ページ内でのみ表示
     */
    private Description $memo;
    private ?DateTimeValue $registered = null;

    /**
     * @param Name $first_name
     * @param Name $last_name
     * @param Email $email
     */
    public function __construct(Name $first_name, Name $last_name, Email $email)
    {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->phone = new Phone();
        $this->first_name_ruby = new Name();
        $this->last_name_ruby = new Name();
        $this->zipcode = new ZipCode('');
        $this->address = new Address('');
        $this->gender = Gender::UNKNOWN;
        $this->memo = new Description();
    }

    /**
     * @return string
     */
    public function get_full_name()
    {
        return $this->first_name->get_value() . ' ' . $this->last_name->get_value();
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
     * @return ID
     */
    public function get_wp_id()
    {
        return $this->wp_id;
    }

    /**
     * @param ID $wp_id
     */
    public function set_wp_id(Id $wp_id)
    {
        $this->wp_id = $wp_id;
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
     * @param Birthday $birthday
     */
    public function set_birthday(Birthday $birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return Description
     */
    public function get_memo()
    {
        return $this->memo;
    }

    /**
     * @param Description $memo
     */
    public function set_memo(Description $memo)
    {
        $this->memo = $memo;
    }

    /**
     * @return DateTimeValue
     */
    public function get_registered()
    {
        return $this->registered;
    }

    /**
     * @param DateTimeValue $registered
     */
    public function set_registered($registered)
    {
        $this->registered = $registered;
    }

    /**
     * @return array
     */
    public function to_table_data()
    {
        $result = $this->to_array();

        unset($result['id']);
        return $result;
    }

    /**
     * @return array
     */
    public function to_array()
    {
        return [
            'id' => $this->get_id()?->get_value(),
            'wp_id' => $this->get_wp_id()?->get_value(),
            'email' => $this->get_email()->get_value(),
            'first_name' => $this->get_first_name()->get_value(),
            'last_name' => $this->get_last_name()->get_value(),
            'first_name_ruby' => $this->get_first_name_ruby()->get_value(),
            'last_name_ruby' => $this->get_last_name_ruby()->get_value(),
            'phone' => $this->get_phone()->get_value(),
            'birthday' => $this->get_birthday()?->get_value(),
            'gender' => $this->get_gender()->value,
            'zipcode' => $this->get_zipcode()->get_value(),
            'address' => $this->get_address()->get_value(),
            'memo' => $this->get_memo()->get_value(),
            'registered' => $this->get_registered()?->get_format_value(),
        ];
    }

    /**
     * エクスポート用の顧客データを取得
     * @return array
     */
    public function to_array_to_export()
    {
        return [
            'ID' => $this->get_id()->get_value(),
            __('Email', 'yoyaku-manager') => $this->get_email()->get_value(),
            __('First Name', 'yoyaku-manager') => $this->get_first_name()->get_value(),
            __('Last Name', 'yoyaku-manager') => $this->get_last_name()->get_value(),
            __('First Name Ruby', 'yoyaku-manager') => $this->get_first_name_ruby()->get_value(),
            __('Last Name Ruby', 'yoyaku-manager') => $this->get_last_name_ruby()->get_value(),
            __('Phone', 'yoyaku-manager') => $this->get_phone()->get_value(),
            __('Birthday', 'yoyaku-manager') => $this->get_birthday()?->get_value() ?: "",
            __('Gender', 'yoyaku-manager') => $this->get_gender()->label(),
            __('Zipcode', 'yoyaku-manager') => $this->get_zipcode()->get_value(),
            __('Address', 'yoyaku-manager') => $this->get_address()->get_value(),
            __('Memo', 'yoyaku-manager') => $this->get_memo()->get_value(),
            __('Registered', 'yoyaku-manager') => $this->get_registered()->get_format_value(),
        ];
    }
}
