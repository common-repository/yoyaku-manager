<?php declare(strict_types=1);

namespace Yoyaku\Domain\WpUser;

use Exception;
use Yoyaku\Domain\Common\AFactory;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;

class WpYoyakuOrganizerFactory extends AFactory
{
    /**
     * Yoyakuの担当者のインスタンスを作成する
     * yoyaku-managerや, administratorは区別していない
     * @param $fields
     * @return WpYoyakuWorker
     * @throws Exception
     */
    public static function create($fields)
    {
        return new WpYoyakuWorker(
            new Id($fields['id']),
            new Email($fields['user_email']),
            new Name($fields['display_name']),
        );
    }
}
