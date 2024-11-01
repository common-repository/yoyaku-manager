<?php declare(strict_types=1);

namespace Yoyaku\Domain\Collection;

use InvalidArgumentException;

/**
 * コレクションの抽象クラス
 */
abstract class ACollection
{
    private array $items;

    /**
     * @param array $items
     * @throws InvalidArgumentException
     */
    public function __construct($items = [])
    {
        $this->items = $items;
    }

    /**
     * コレクションにアイテムを追加する。
     * @param mixed $item
     * @param mixed $key 追加するキー
     * @throws InvalidArgumentException
     */
    public function add_item($item, $key = null)
    {
        if ($key) {
            $this->items[$key] = $item;
        } else {
            $this->items[] = $item;
        }
    }

    /**
     * 指定されたキーがコレクションに存在するかどうかをチェックする
     * @param $key
     * @return bool
     */
    public function key_exists($key)
    {
        return isset($this->items[$key]);
    }

    /**
     * 特定のコレクションのアイテムを削除する
     * @param $key
     * @throws InvalidArgumentException
     */
    public function delete_item($key)
    {
        if (!$this->key_exists($key)) {
            throw new InvalidArgumentException("Invalid collection key.");
        }

        unset($this->items[$key]);
    }

    /**
     * 特定のコレクションのアイテムを取得する
     * @param $key
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get_item($key)
    {
        if (!$this->key_exists($key)) {
            throw new InvalidArgumentException("Invalid collection key.");
        }
        return $this->items[$key];
    }

    /**
     * @return array
     */
    public function get_items()
    {
        return $this->items;
    }

    /**
     * キーのリストを取得
     * @return array
     */
    public function keys()
    {
        return array_keys($this->items);
    }

    /**
     * アイテム数を取得
     * @return int
     */
    public function length()
    {
        return count($this->items);
    }

    /**
     * 配列に変換したコレクションを取得
     * @param bool $is_front
     * @return array
     */
    public function to_array($is_front = false)
    {
        $result = [];
        foreach ($this->items as $item) {
            $result[] = $item->to_array($is_front);
        }

        return $result;
    }

    /**
     * 配列に変換したコレクションを取得
     * @return array
     */
    public function to_array_for_customer()
    {
        $result = [];
        foreach ($this->items as $item) {
            if (method_exists($item, 'to_array_for_customer')) {
                $result[] = $item->to_array_for_customer();
            }
        }

        return $result;
    }
}
