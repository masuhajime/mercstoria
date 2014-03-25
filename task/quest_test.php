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
                $quest = new \app\model\Quest(32);
                $quest->setDifficulty('very_hard');
            } else {
                $quest_setting = $queue->get();
                Logger::info("queued quest start:{$quest_setting["id"]}:{$quest_setting["difficulty"]}");
                $quest = new \app\model\Quest($quest_setting['id']);
                $quest->setDifficulty($quest_setting['difficulty']);
            }
            //$quest = new \app\model\Quest(116);// 113=easy,114,115,116
            //$quest->setDifficulty('very_hard');
            break;
    }
    $player = new \app\model\Player(CONFIG::APP_ID_1, CONFIG::APP_ID_2);
    $quest = $player->questStart($quest);
    Logger::info('ap_use:'.$quest->ap_use_url);
    Logger::info('result:'.$quest->result_url);
    Logger::info('end');
}