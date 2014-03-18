<?php

namespace app\model;

class Quest
{
    private $quest_id = null;
    private $success_start = null;
    private $success_end = null;
    private $time = null;
    private $difficulty = null;
    
    // めんどくさくなった
    public $ap_use_url = null;
    public $result_url = null;
    
    public function __construct($quest_id) {
        $this->quest_id = $quest_id;
    }
    
    public function setTime($int) {$this->time = $int;}
    public function getTime() {return $this->time;}
    
    public function setDifficulty($difficulty) {$this->difficulty = $difficulty;}
    public function getDifficulty() {return $this->difficulty;}
    
    public function getQuestId(){return $this->quest_id;}
    public function setSucceedStart($bool) {$this->success_start = $bool;}
    public function isSucceedStart() {return $this->success_start;}
    
    public function setSucceedEnd($bool) {$this->success_end = $bool;}
    public function isSucceedEnd() {return $this->success_end;}
}
