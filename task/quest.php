<?php
require_once __DIR__.'/../app/bootstrap.php';

use app\helper\Logger;

main();

function main()
{
    // petar death: 112
    $quest = new \app\model\Quest(112);
    $quest->setTime(mt_rand(120, 180));
    
    Logger::info('start');
    $player = new \app\model\Player(CONFIG::APP_ID_1, CONFIG::APP_ID_2);
    $quest = $player->questStart($quest);
    if (!$quest->isSucceedStart()) {
        return false;
    }
    Logger::info("sleep: {$quest->getTime()}sec");
    sleep($quest->getTime());
    $quest = $player->questEnd($quest);
    Logger::info('end');
}