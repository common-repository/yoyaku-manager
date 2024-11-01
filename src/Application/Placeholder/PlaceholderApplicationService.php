<?php declare(strict_types=1);

namespace Yoyaku\Application\Placeholder;

class PlaceholderApplicationService
{
    /**
     * @param string $text
     * @param array $data
     * @return array|string|string[]
     */
    public function apply_placeholders($text, $data)
    {
        $placeholders = array_map(fn($placeholder) => "%{$placeholder}%", array_keys($data));
        return str_replace($placeholders, array_values($data), $text);
    }
}
