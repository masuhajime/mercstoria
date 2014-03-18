<?php

namespace app\model\queue;

class FileQueue extends BaseQueue
{
    private $file = null;
    private $queues = null;
    
    public function get()
    {
        return reset($this->queues);
    }
    
    public function pop()
    {
        $return = array_shift($this->queues);
        $this->save();
        return $return;
    }
    
    private function save()
    {
        $text = implode(PHP_EOL, $this->queues);
        return file_put_contents($this->file, $text);
    }
    
    public function __construct($file) {
        if (!file_exists($file)) {
            throw new \RuntimeException("file not exists:{$file}");
        }
        $this->set($file);
    }
    
    public function set($file)
    {
        $this->file = $file;
        $this->queues = file($this->file);
        $this->queues = array_map('trim', $this->queues);
    }
    
    protected static function isData($string)
    {
        return !(is_null($string) || 0 === strlen(trim($string)));
    }
}
