<?php
namespace FlatSite;

class Config
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function set($key, $value)
    {
        $this->config[$key] = $value;
    }

    public function get($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }
}