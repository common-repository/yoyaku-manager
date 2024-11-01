<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage;

class Events extends ASubmenuPage
{
    public function __construct()
    {
        parent::__construct(
            'Events',
            __('Events', 'yoyaku-manager'),
            'yoyaku_read_events',
            'yoyaku-events',
        );
    }
}
