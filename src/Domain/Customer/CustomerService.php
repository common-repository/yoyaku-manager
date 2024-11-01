<?php declare(strict_types=1);

namespace Yoyaku\Domain\Customer;

use Yoyaku\Application\Common\Exceptions\DuplicationError;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Common\AEntityService;
use Yoyaku\Domain\DateTime\DateTimeService;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Infrastructure\Repository\Customer\CustomerRepository;

class CustomerService extends AEntityService
{
    public function __construct(CustomerRepository $repo)
    {
        parent::__construct($repo, CustomerFactory::class);
    }

    /**
     * DBに追加できるユーザーかチェックする。
     * emailの重複がなければ追加できる。
     * @param Email $email
     * @return bool 追加可能ならtrue. emailが空文字でない場合や、重複しているならfalse
     */
    public function can_add($email)
    {
        if (!$email->get_value()) {
            return false;
        }
        return !$this->repo->exists_by_email($email->get_value());
    }

    /**
     * 顧客を追加する
     * @param array $fields
     * @return int
     * @throws WpDbException
     * @throws DuplicationError
     */
    public function add($fields)
    {
        if (!$this->can_add(new Email($fields['email']))) {
            throw new DuplicationError(esc_html__('This email is already in use.', 'yoyaku-manager'));
        }

        $fields['registered'] = DateTimeService::get_now_datetime_in_utc();

        return parent::add($fields);
    }
}
