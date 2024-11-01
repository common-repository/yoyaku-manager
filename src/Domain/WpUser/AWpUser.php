<?php declare(strict_types=1);

namespace Yoyaku\Domain\WpUser;

use Yoyaku\Domain\UserRole\UserRole;
use Yoyaku\Domain\ValueObject\Number\Id;
use Yoyaku\Domain\ValueObject\String\Email;
use Yoyaku\Domain\ValueObject\String\Name;

/**
 * wpの権限グループを持つユーザークラス Entity
 */
abstract class AWpUser
{
    protected Id $id;
    protected Name $display_name;
    protected Email $user_email;

    /**
     * @param Id $id
     * @param Email $user_email
     * @param Name $display_name
     */
    public function __construct(Id $id, Email $user_email, Name $display_name)
    {
        $this->id = $id;
        $this->display_name = $display_name;
        $this->user_email = $user_email;
    }

    /**
     * @return UserRole
     */
    abstract public function get_role();

    /**
     * @return Id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @param Id $id
     */
    public function set_id(Id $id)
    {
        $this->id = $id;
    }

    /**
     * @return Email
     */
    public function get_user_email()
    {
        return $this->user_email;
    }

    /**
     * @return Name
     */
    public function get_display_name()
    {
        return $this->display_name;
    }

    /**
     * @return array
     */
    public function to_array()
    {
        return [
            'id' => $this->get_id()->get_value(),
            'display_name' => $this->get_display_name()->get_value(),
            'user_email' => $this->get_user_email()->get_value(),
            'role' => $this->get_role()->translated(),
        ];
    }
}
