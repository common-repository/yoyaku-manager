<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage;


class Customers extends ASubmenuPage
{
    public function __construct()
    {
        parent::__construct(
            'Customers',
            __('Customers', 'yoyaku-manager'),
            'yoyaku_read_customers',
            'yoyaku-customers',
        );
    }
}
