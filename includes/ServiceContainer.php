<?php

namespace PostBookmark;

use Exception;

defined('WPINC') || die;

class ServiceContainer
{
    private array $bindings = [];

    public function bind(string $key, callable $builder)
    {
        $this->bindings[$key] = ['builder' => $builder, 'instance' => null];
    }

    public function get(string $key)
    {
        if (!isset($this->bindings[$key])) {
            throw new Exception("Service not found: {$key}");
        }

        if ($this->bindings[$key]['instance'] === null) {
            $builder = $this->bindings[$key]['builder'];
            $this->bindings[$key]['instance'] = $builder($this);
        }

        return $this->bindings[$key]['instance'];
    }
}
