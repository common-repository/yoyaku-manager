<?php declare(strict_types=1);

namespace Yoyaku\Domain\Worker;

/**
 * google OAuth2.0 のレスポンスのaccess tokenを保持するクラス
 * Class AccessToken
 */
final class AccessToken
{
    private string $token;

    /**
     * @param string $token
     */
    public function __construct($token = '')
    {
        $this->token = $token;
    }

    public function get_value(): string
    {
        return $this->token;
    }
}
