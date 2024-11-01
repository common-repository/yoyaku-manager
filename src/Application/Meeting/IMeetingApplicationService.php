<?php declare(strict_types=1);

namespace Yoyaku\Application\Meeting;

/**
 *
 */
interface IMeetingApplicationService
{
    public function add_meeting($period_id);

    public function update_meeting($period_id);

    public function delete_google_meeting($period_id);

    public function delete_zoom_meeting($period_id);
}
