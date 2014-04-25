<?php
require_once __DIR__.'/../app/bootstrap.php';

use app\helper\Logger;

main();

function main()
{
    $player = new \app\model\Player(CONFIG::APP_ID_1, CONFIG::APP_ID_2);
   
    $id = get_most_point_id($player);
    
    $result = $player->getBattleExecute($id);
    var_dump($result);
    sleep(0);
    var_dump($player->battleBpUse($result[0]));
    $time = 5;//mt_rand(10, 10);
    $sleep_time = intval($time);
    Logger::info("sleep:{$sleep_time}", __LINE__, __FILE__);
    sleep($sleep_time);
    //sleep(230);
    var_dump($player->battleResult($result[1], $time));
}

function get_most_point_id(app\model\Player $player)
{
        // 最もptの高いギルドを探す
    $guilds_datas = $player->getGuildsBattleStatus();
    $guild_data = null;
    foreach ($guilds_datas as $data) {
        if (true === $data['joined']) {
            continue;
        }
        if (is_null($guild_data) || $data['point'] > $guild_data['point']) {
            $guild_data = $data;
            continue;
        }
    }
    if (is_null($guild_data)) {
        Logger::info("guild_data is null", __LINE__, __FILE__);
        return 0;
    }
    Logger::info("attack to {$guild_data['name']}({$guild_data['point']}pt/{$guild_data['id']})", __LINE__, __FILE__);
    sleep(1);
    
    return $guild_data['id'];
}