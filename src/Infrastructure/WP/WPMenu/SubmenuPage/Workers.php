<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage;

class Workers extends ASubmenuPage
{
    public function __construct()
    {
        parent::__construct(
            'Workers',
            __('Workers', 'yoyaku-manager'),
            'yoyaku_read_workers',
            'yoyaku-workers',
        );
    }
}
