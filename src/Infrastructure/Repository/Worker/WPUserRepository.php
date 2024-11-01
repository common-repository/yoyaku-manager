<?php declare(strict_types=1);

namespace Yoyaku\Infrastructure\Repository\Worker;

use Yoyaku\Application\Common\Exceptions\DataNotFoundException;
use Yoyaku\Application\Common\Exceptions\WpDbException;
use Yoyaku\Domain\Collection\Collection;
use Yoyaku\Domain\UserRole\UserRole;
use Yoyaku\Domain\WpUser\AWpUser;
use Yoyaku\Domain\WpUser\WpUserFactory;
use Yoyaku\Domain\WpUser\WpYoyakuWorker;

/**
 *　wpのusersテーブルを管理するリポジトリ
 */
class WPUserRepository
{
    const FACTORY = WpUserFactory::class;

    /**
     * 特定のユーザーを取得する。 存在しない場合は WpGuest を返す
     * @param int $id
     * @return WpYoyakuWorker
     * @throws DataNotFoundException
     */
    public function get_by_id($id)
    {
        $wp_user = get_user_by('id', $id);
        if (!$wp_user) {
            throw new DataNotFoundException();
        }

        $wp_user_role = UserRole::get_user_yoyaku_role($wp_user);
        if ($wp_user_role === UserRole::GUEST) {
            throw new DataNotFoundException();
        }

        $current_user = $this->get_current_user();
        // yoyaku-workerはyoyaku-worker以外のユーザーを取得できない
        if ($current_user->get_role() === UserRole::WORKER && $wp_user_role !== UserRole::WORKER) {
            throw new DataNotFoundException();
        }
        // yoyaku-managerはadminを取得できない
        if ($current_user->get_role() === UserRole::MANAGER && $wp_user_role === UserRole::ADMIN) {
            throw new DataNotFoundException();
        }

        return call_user_func(
            [static::FACTORY, 'create'],
            [
                'id' => $wp_user->ID,
                'display_name' => $wp_user->get('display_name'),
                'user_email' => $wp_user->get('user_email'),
                'role' => UserRole::get_user_yoyaku_role($wp_user),
            ]
        );
    }

    /**
     * 現在リクエストしているユーザーを取得
     * @return AWpUser
     */
    public static function get_current_user()
    {
        $wp_user = wp_get_current_user();
        if ($wp_user->ID === 0) {
            // ログインしていない時のIDは0になる
            return WpUserFactory::create(['type' => UserRole::GUEST]);
        }

        return call_user_func(
            [static::FACTORY, 'create'],
            [
                'role' => UserRole::get_user_yoyaku_role($wp_user),
                'id' => $wp_user->ID,
                'display_name' => $wp_user->get('display_name'),
                'user_email' => $wp_user->get('user_email'),
            ]
        );
    }

    /**
     * yoyakuの権限グループのwpユーザーを取得
     * @param array $filter
     * @return Collection<WpYoyakuWorker>|int 検索条件にヒットしたユーザーデータ
     * @throws WpDbException
     */
    public function filter($filter, $count = false)
    {
        $current_user = $this->get_current_user();
        $args = [];
        if ($current_user->get_role() === UserRole::ADMIN) {
            $args['role__in'] = [
                'administrator',
                UserRole::MANAGER->get_wp_role_name(),
                UserRole::WORKER->get_wp_role_name(),
            ];
        } elseif ($current_user->get_role() === UserRole::MANAGER) {
            $args['role__in'] = [
                UserRole::MANAGER->get_wp_role_name(),
                UserRole::WORKER->get_wp_role_name(),
            ];
        } elseif ($current_user->get_role() === UserRole::WORKER) {
            $args['role__in'] = [
                UserRole::WORKER->get_wp_role_name(),
            ];
        }

        if (!empty($filter['search'])) {
            $args['search'] = '*' . $filter['search'] . '*';
            $args['search_columns'] = ['user_email', 'display_name'];
        }

        if (!empty($filter['orderby']) && !empty($filter['order'])) {
            $args['orderby'] = $filter['orderby'];
            $args['order'] = $filter['order'];
        }

        if ($count) {
            return count(get_users($args));
        } else {
            if (isset($filter['page'], $filter['per_page'])) {
                $args['number'] = $filter['per_page'];
                $args['paged'] = $filter['page'];
            }
            $users = get_users($args);

            $result = [];
            foreach ($users as $user) {
                $result[] = [
                    'id' => $user->ID,
                    'user_email' => $user->user_email,
                    'display_name' => $user->display_name,
                    'role' => UserRole::get_user_yoyaku_role($user),
                ];
            }

            return call_user_func(
                [static::FACTORY, 'create_collection'],
                $result,
            );
        }
    }
}
