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
        $options = [
            'name' => 'Quest',
            'base' => 'Quest/Quest',
            'mode' => 'quest',
            'tipsLoading' => 'true',
            'id' => $quest->getQuestId(),
            'difficulty_id' => $quest->getDifficulty(),
            'party_id' => '001',
            'unit_ids' => '1,2,3,4,5,6,7,10,13,14,17,25,41,43,44,48,54,57,61,66,68,71,75,',
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