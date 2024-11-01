<?php declare(strict_types=1);

namespace Yoyaku\Domain\WpUser;

use Yoyaku\Domain\UserRole\UserRole;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;
use Yoyaku\Domain\Worker\Worker;

/**
 * yoyaku権限を持つユーザークラス Entity
 */
class BaseWpOrganizer extends AWpUser
{
    private ?Worker $worker = null;

    /**
     * @param Id $id
     * @param Email $user_email
     * @param Name $display_name
     */
    public function __construct(Id $id, Email $user_email, Name $display_name)
    {
        parent::__construct($id, $user_email, $display_name);
    }

    public function get_role()
    {
        // 仮の権限
        return UserRole::WORKER;
    }

    /**
     * @return Worker
     */
    public function get_worker()
    {
        return $this->worker;
    }

    /**
     * @param Worker $worker
     */
    public function set_worker(Worker $worker)
    {
        $this->worker = $worker;
    }

    private function get_worker_array()
    {
        if ($this->worker) {
            return $this->worker->to_array();
        } else {
            return [
                'zoom_user_id' => '',
                'google_calendar_id' => '',
                'google_calendar_token' => '',
            ];
        }
    }

    /**
     * @return array
     */
    public function to_array()
    {
        return array_merge(
            parent::to_array(),
            $this->get_worker_array(),
        );
    }
}
