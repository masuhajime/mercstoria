<?php
require_once __DIR__.'/../app/bootstrap.php';

use app\helper\Logger;

main();

function main()
{
    Logger::info('start');
    $queue_file = DIR_DATA.'/quest_queue';
    $queue = new \app\model\queue\QuestQueue($queue_file);
    //var_dump($queue->get());
    //var_dump($queue->pop());
    
    // (土曜日)gold quest normal: 101
    // (日曜日)gold quest normal: 105
    
    // 'normal', 'hard', 'very_hard'
    switch(intval(date('w'))) {
        case 6: //　土曜日
            $quest = new \app\model\Quest(102);// gold quest
            $quest->setDifficulty('normal');
            break;
        case 0: //　日曜日
            $quest = new \app\model\Quest(106);// gold quest
            $quest->setDifficulty('normal');
            break;
        case 1: 
        case 2: 
        case 3: 
        case 4: 
        case 5: 
            $queue = new \app\model\queue\QuestQueue($queue_file);
            if (!$queue->hasQuest()) {
                $quest = new \app\model\Quest(79);
                $quest->setDifficulty('very_hard');
            } else {
                $queued_quest = true;
                $quest_setting = $queue->get();
                Logger::info("queued quest start:{$quest_setting["id"]}:{$quest_setting["difficulty"]}");
                $quest = new \app\model\Quest($quest_setting['id']);
                $quest->setDifficulty($quest_setting['difficulty']);
            }
            //$quest = new \app\model\Quest(116);// 113=easy,114,115,116
            //$quest->setDifficulty('very_hard');
            break;
    }
    $quest->setTime(mt_rand(200, 230));
    
    $player = new \app\model\Player(CONFIG::APP_ID_1, CONFIG::APP_ID_2);
    if (true) {// quest start
        $quest = $player->questStart($quest);
        if (!$quest->isSucceedStart()) {
            return false;
        }
        sleep(3);
        $player->questApUse($quest);
        Logger::info("sleep: {$quest->getTime()}sec");
        sleep($quest->getTime());
    }
    $quest = $player->questEnd($quest);
    
    if ($quest->isSucceedEnd() && isset($queued_quest) && $queued_quest === true) {
        $quest_setting = $queue->pop();
        Logger::info("queued quest finished:{$quest_setting["id"]}:{$quest_setting["difficulty"]}");
    }
    Logger::info('end');
}