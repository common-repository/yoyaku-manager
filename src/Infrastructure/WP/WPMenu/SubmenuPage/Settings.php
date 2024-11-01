<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\WP\WPMenu\SubmenuPage;

class Settings extends ASubmenuPage
{
    public function __construct()
    {
        parent::__construct(
            'Settings',
            __('Settings', 'yoyaku-manager'),
            'manage_options',
            'yoyaku-settings',
        );
    }
}
