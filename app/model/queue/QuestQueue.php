<?php

namespace app\model\queue;

class QuestQueue extends FileQueue
{   
    private static $_DELIMITOR = ',';
    
    public function hasQuest()
    {
        return !is_null($this->get());
    }
    
    public function get()
    {
        $result = parent::get();
        if (!self::isData($result)) {
            return null;
        }
        $result = explode(self::$_DELIMITOR, $result);
        return [
            'id' => $result[0],
            'difficulty' => $result[1],
        ];
    }

    public function pop()
    {
        $result = parent::pop();
        if (!self::isData($result)) {
            return null;
        }
        $result = explode(self::$_DELIMITOR, $result);
        return [
            'id' => $result[0],
            'difficulty' => $result[1],
        ];
    }

    public function __construct($file)
    {
        parent::__construct($file);
    }
}
