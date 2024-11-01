<?php declare(strict_types=1);

namespace Yoyaku\Domain\Customer;

use InvalidArgumentException;

/**
 * 郵便番号 空文字許可
 * 調査によると文字数はアメリカの郵便番号の10文字(ハイフンあり)が最大だが、20文字入力可能にする
 * 英数字、空白、ハイフンが使える
 */
final class ZipCode
{
    const MAX_LENGTH = 20;
    private string $phone;

    /**
     * @param string $zipcode
     * @throws InvalidArgumentException
     */
    public function __construct($zipcode = '')
    {
        if ($zipcode && self::MAX_LENGTH < strlen($zipcode)) {
            throw new InvalidArgumentException('Phone must be less than 20 chars');
        }
        if ($zipcode && !preg_match('/^[ \-\da-zA-Z]*$/', $zipcode)) {
            throw new InvalidArgumentException("Invalid zip code. 0-9 a-z A-Z - space, can use.");
        }
        $this->phone = $zipcode;
    }

    public function get_value(): string
    {
        return $this->phone;
    }
}
