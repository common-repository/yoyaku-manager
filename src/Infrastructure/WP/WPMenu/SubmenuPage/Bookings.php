<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage;

class Bookings extends ASubmenuPage
{
    public function __construct()
    {
        parent::__construct(
            'Bookings',
            __('Bookings', 'yoyaku-manager'),
            'yoyaku_read_bookings',
            'yoyaku-bookings',
        );
    }
}
