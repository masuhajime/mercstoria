<?php
require_once __DIR__.'/../app/bootstrap.php';

use app\helper\Logger;

main();

function main()
{
    $player = new \app\model\Player(CONFIG::APP_ID_1, CONFIG::APP_ID_2);
    var_dump($player->getGuildsBattleStatus());
}