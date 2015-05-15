<?php
namespace FlatSite\Storage;

interface StorageInterface
{
    public function read($key);
    public function write($key, $value);
    public function delete($key);
}