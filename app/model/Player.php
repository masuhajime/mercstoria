<?php

namespace app\model;

class Player extends User
{
    private $APP_ID_1 = null;
    private $APP_ID_2 = null;
    
    public function __construct($APP_ID_1, $APP_ID_2)
    {
        $this->APP_ID_1 = $APP_ID_1;
        $this->APP_ID_2 = $APP_ID_2;
        
        parent::__construct("[player name]");
    }
    
    public function questEnd(Quest $quest)
    {
        $options = [
            'time' => "{$quest->getTime()}.".mt_rand(10000, 99999),
        ];
        $query = http_build_query($options);
        $url = $quest->result_url.'&'.$query;
        \app\helper\Logger::info('quest end:'.$url, __LINE__, __FILE__);
        $client = $this->getClient();
        $request = $client->post($url, null, array(
            '_method' => 'GET',
        ));
        $result = $request->send();
        $json = json_decode($result->getBody(), true);
        if (is_null($json)) {
            \app\helper\Logger::warning('quest end failed', __LINE__, __FILE__);
            $quest->setSucceedEnd(false);
            return $quest;
        }
        if (isset($json['data']['user'])) {
            \app\helper\Logger::info('quest end success', __LINE__, __FILE__);
            $quest->setSucceedEnd(true);
            /*
"leader_unit_id":192,
"exp":8508,
"level":12,
"ap":0,
"max_ap":31,
"ap_updated_at":"2014-03-06T16:05:56+09:00",
"bp":300,
"medal":31,
"gacha_point":8756,
"guild_id":"dc3ef5b4-9d2b-11e3-8159-dee0a925659e",
"guild_asset_count":10870,
"guild_joined_at":"2014-03-05T15:22:58+09:00",
"community_topic_id":17,
"stage_id":2,
"area_id":8,
"quest_id":30,
             */
            // $json['data']['user']['gacha_point']; gacha point
            return $quest;
        } else {
            \app\helper\Logger::warning('quest end failed', __LINE__, __FILE__);
            $quest->setSucceedEnd(false);
            return $quest;
        }
    }
    
    public function questStart(Quest $quest)
    {
        //  http://toto.hekk.org/quests/execute/1.json?name=Quest&base=Quest/Quest&mode=quest&tipsLoading=true&id=1&difficulty_id=normal&party_id=001&unit_ids=1,2,3,4,5,6,7,10,13,14,17,25,41,43,44,48,54,57,61,66,68,71,75,
        // &fever_drop_rate_ok=true
        $options = [
            'name' => 'Quest',
            'base' => 'Quest/Quest',
            'mode' => 'quest',
            'tipsLoading' => 'true',
            'id' => $quest->getQuestId(),
            'difficulty_id' => $quest->getDifficulty(),
            'party_id' => '001',
            'unit_ids' => '1,2,3,4,5,6,7,10,13,14,17,25,41,43,44,48,54,57,61,66,68,71,75,',
            'fever_drop_rate_ok' => 'true',//文字列で送信しているため
        ];
        $query = http_build_query($options);
        $url = '/quests/execute/'.$quest->getQuestId().'.json?'.$query;
        \app\helper\Logger::info('quest start:'.$url, __LINE__, __FILE__);
        $client = $this->getClient();
        $request = $client->post($url, null, array(
            '_method' => 'GET',
        ));
        $result = $request->send();
        $json = json_decode($result->getBody(), true);
        if (is_null($json)) {
            \app\helper\Logger::warning('quest start failed', __LINE__, __FILE__);
            $quest->setSucceedStart(false);
            return $quest;
        }
        if (isset($json['user'])) {
            \app\helper\Logger::info('quest start success', __LINE__, __FILE__);
            $quest->setSucceedStart(true);
            $quest->ap_use_url = $json['ap_use_url'];
            $quest->result_url = $json['result_url'];
            return $quest;
        } else {
            \app\helper\Logger::warning('quest start failed', __LINE__, __FILE__);
            $quest->setSucceedStart(false);
            return $quest;
        }
    }

    public function questApUse(Quest $quest)
    {
        /*
        $url = 'quests/ap_use';
        $options = [
            'id' => $quest->getQuestId(),
            'difficulty_id' => $quest->getDifficulty(),
        ];
        $query = http_build_query($options);
        $url = 'quests/ap_use?'.$query;
         */
        $url = $quest->ap_use_url;
        \app\helper\Logger::info('ap use:'.$url, __LINE__, __FILE__);
        $client = $this->getClient();
        $request = $client->post($url, null, array(
            '_method' => 'GET',
        ));
        $result = $request->send();
        return $result->getBody();
    }

    public function loginBonus()
    {
        // POST Full request URI: http://toto.hekk.org/users/receive_login_bonus
        $url = '/users/receive_login_bonus';
        \app\helper\Logger::info('login bonus:'.$url, __LINE__, __FILE__);
        $client = $this->getClient();
        $request = $client->post($url, null, array(
            '_method' => 'GET',
        ));
        $result = $request->send();
        return $result->getBody();
    }

    public function battleHelp()
    {
        // POST Full request URI: http://toto.hekk.org/users/receive_login_bonus
        $url = '/battles/help';
        \app\helper\Logger::info('battle help:'.$url, __LINE__, __FILE__);
        $client = $this->getClient();
        $request = $client->post($url, null, array(
            '_method' => 'GET',
        ));
        $result = $request->send();
        return $result->getBody();
    }

    public function getGuildsBattleStatus()
    {
        $url = '/guilds/battle_condition';
        \app\helper\Logger::info('guilds:'.$url, __LINE__, __FILE__);
        $client = $this->getClient();
        $request = $client->post($url, null, array(
            '_method' => 'GET',
        ));
        $result = $request->send();
        $json = json_decode($result->getBody(), true);
        if (is_null($json)) {
            throw new \RuntimeException("111");
        }
        return $json['data']['battle_condition']['guilds'];
    }

    public function getBattleExecute($guild_id)
    {
        // POST /battles/guild_quest_execute.json?
        // name=Quest&
        // base=Quest/Quest&
        // mode=battle&
        // tipsLoading=true&
        // guild_id=d272d3c4-8dff-11e3-9ee4-00b6b314b725&
        // difficulty_id=normal&
        // party_id=001&
        // unit_ids=8,17,18,19,20,22,23,24,25,26,27,28,29,30,31,32,33,34,36,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,59,60,61,63,64,66,67,68,70,71,72,74,75,76,77,78,79,80,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,102,104,109,130,147,232,233,244,245,276&
        // page_message=name%3dQuest%26base%3dQuest%2fQuest%26mode%3dbattle%26tipsLoading%3dtrue%26guild_id%3dd272d3c4-8dff-11e3-9ee4-00b6b314b725%26difficulty_id%3dnormal%26party_id%3d001%26unit_ids%3d8%2c17%2c18%2c19%2c20%2c22%2c23%2c24%2c25%2c26%2c27%2c28%2c29%2c30%2c31%2c32%2c33%2c34%2c36%2c38%2c39%2c40%2c41%2c42%2c43%2c44%2c45%2c46%2c47%2c48%2c49%2c50%2c51%2c52%2c53%2c54%2c55%2c56%2c57%2c59%2c60%2c61%2c63%2c64%2c66%2c67%2c68%2c70%2c71%2c72%2c74%2c75%2c76%2c77%2c78%2c79%2c80%2c82%2c83%2c84%2c85%2c86%2c87%2c88%2c89%2c90%2c91%2c92%2c93%2c94%2c95%2c96%2c102%2c104%2c109%2c130%2c147%2c232%2c233%2c244%2c245%2c276
        $options = [
            'name' => 'Quest',
            'base' => 'Quest/Quest',
            'mode' => 'battle',
            'tipsLoading' => 'true',
            'guild_id' => $guild_id,
            'party_id' => '001',
            'difficulty_id' => 'very_hard',
            'unit_ids' => '1,2,3,4,5,6,7,10,13,14,17,25,41,43,44,48,54,57,61,66,68,71,75,',
        ];
        $query = http_build_query($options);
        $url = '/battles/guild_quest_execute.json?'.$query;
        \app\helper\Logger::info('battle start:'.$url, __LINE__, __FILE__);
        $client = $this->getClient();
        $request = $client->post($url, null, array(
            '_method' => 'GET',
        ));
        $result = $request->send();
        $json = json_decode($result->getBody(), true);
        if (is_null($json)) {
            \app\helper\Logger::warning('battle start failed', __LINE__, __FILE__);
            throw new \RuntimeException('battle start failed');
        }
        return [$json['ap_use_url'], $json['result_url']];
    }
    
    public function battleBpUse($url)
    {
        \app\helper\Logger::info('battle bp use:'.$url, __LINE__, __FILE__);
        $client = $this->getClient();
        $request = $client->post($url, null, array(
            '_method' => 'GET',
        ));
        $result = $request->send();
        $json = json_decode($result->getBody(), true);
        if (is_null($json)) {
            \app\helper\Logger::warning('bp use failed', __LINE__, __FILE__);
            throw new \RuntimeException('bp use failed');
        }
        return $json;
    }
    
    public function battleResult($url, $time)
    {
        $options = [
            'time' => "{$time}.".mt_rand(1000, 9999),
        ];
        $query = http_build_query($options);
        $url = $url.'&'.$query;
        \app\helper\Logger::info('battle bp use:'.$url, __LINE__, __FILE__);
        $client = $this->getClient();
        $request = $client->post($url, null, array(
            '_method' => 'GET',
        ));
        $result = $request->send();
        $json = json_decode($result->getBody(), true);
        if (is_null($json)) {
            \app\helper\Logger::warning('bp use failed', __LINE__, __FILE__);
            throw new \RuntimeException('bp use failed');
        }
        return $json;
    }
    
    // 途中
    public function getStatus()
    {
        $client = $this->getClient();
        $time = time();
        $request = $client->post('/users/messages?last_read_at='.$time.'&page_type=Home', null, array(
            '_method' => 'GET',
        ));
        $result = $request->send();
        echo $result->getBody();
    }

    private function getClient()
    {
        return Request::getClient($this->APP_ID_1, $this->APP_ID_2);
    }
}