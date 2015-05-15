<?php
namespace FlatSite\Storage;

use FlatSite\Config;

class FileSystem implements StorageInterface
{
    protected $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function write($filePath, $contents)
    {
        $h = fopen($filePath, 'w');
        fwrite($h, $contents);
        fclose($h);
    }

    public function read($filePath)
    {
        $h = fopen($filePath, 'r');
        $content = fread($h, filesize($filePath));
        return $content;
    }

    public function delete($filePath)
    {

    }
}