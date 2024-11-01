<?php declare(strict_types=1);

namespace Yoyaku\Domain\Customer;

use Exception;
use Yoyaku\Domain\Common\AFactory;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\ValueObject\DateTime\DateTimeValue;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Address;
use Yoyaku\Domain\ValueObject\String\Description;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Domain\ValueObject\String\Phone;

class CustomerFactory extends AFactory
{
    /**
     * @param $fields
     * @return Customer
     * @throws Exception
     */
    public static function create($fields)
    {
        $user = new Customer(
            new Name(trim($fields['first_name'])),
            new Name(trim($fields['last_name'])),
            new Email($fields['email']),
        );

        if (!empty($fields['first_name_ruby'])) {
            $user->set_first_name_ruby(new Name($fields['first_name_ruby']));
        }

        if (!empty($fields['last_name_ruby'])) {
            $user->set_last_name_ruby(new Name($fields['last_name_ruby']));
        }

        if (!empty($fields['id'])) {
            $user->set_id(new Id($fields['id']));
        }

        if (!empty($fields['wp_id'])) {
            $user->set_wp_id(new Id($fields['wp_id']));
        }

        if (!empty($fields['phone'])) {
            $user->set_phone(new Phone($fields['phone']));
        }

        if (!empty($fields['zipcode'])) {
            $user->set_zipcode(new ZipCode($fields['zipcode']));
        }

        if (!empty($fields['address'])) {
            $user->set_address(new Address($fields['address']));
        }

        if (!empty($fields['birthday'])) {
            $user->set_birthday(new Birthday($fields['birthday']));
        }

        if (!empty($fields['gender'])) {
            $user->set_gender(Gender::tryFrom($fields['gender']));
        }

        if (!empty($fields['memo'])) {
            $user->set_memo(new Description($fields['memo']));
        }

        if (!empty($fields['registered'])) {
            $user->set_registered(
                new DateTimeValue(DateTimeService::get_custom_datetime_object_from_utc($fields['registered']))
            );
        }

        return $user;
    }
}
