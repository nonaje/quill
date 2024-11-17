<?php

declare(strict_types=1);

namespace Quill\Config;

use Quill\Contracts\Configuration\ConfigurationInterface;
use Quill\Support\Traits\Singleton;

class Config implements ConfigurationInterface
{
    use Singleton;

    private array $items = [];

    /** @ineritDoc */
    public function all(): array
    {
        return $this->items;
    }

    /** @inheritDoc */
    public function get(string $key, mixed $default = null): mixed
    {
        foreach ($this->fromDotNotationToArray($key) as $key) {
            $value = $this->items[$key] ?? $value[$key] ?? null;

            if (is_null($value)) return $default;
        }

        return $value ?? $default;
    }

    /** @inheritDoc */
    public function push(string $key, mixed $value): ConfigurationInterface
    {
        $items = &$this->items;

        foreach ($this->fromDotNotationToArray($key) as $key) {
            if (!isset($items[$key])) {
                $items[$key] = null;
            }

            $items = &$items[$key];
        }

        if (is_null($items)) {
            $items = $value;
            return $this;
        }

        if (is_array($items)) {
            $items[] = $value;
            return $this;
        }

        $items = [$items, $value];

        return $this;
    }

    /** @inheritDoc */
    public function put(string $key, mixed $value): ConfigurationInterface
    {
        $items = &$this->items;

        foreach ($this->fromDotNotationToArray($key) as $key) {
            if (!isset($items[$key]) || !is_array($items[$key])) {
                $items[$key] = [];
            }

            $items = &$items[$key];
        }

        $items = $value;

        return $this;
    }

    /**
     * Convert the dot notation key into an array
     *
     * @param string $key
     * @return array
     */
    private function fromDotNotationToArray(string $key): array
    {
        $key = strtolower($key);
        return array_values(array_filter(explode('.', $key))) ?: [$key];
    }
}
