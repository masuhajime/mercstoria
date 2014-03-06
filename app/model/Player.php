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
            'id' => $quest->getQuestId(),
            'time' => "{$quest->getTime()}.".mt_rand(100, 999),
        ];
        $query = http_build_query($options);
        $client = $this->getClient();
        $url = '/quests/result?'.$query;
        \app\helper\Logger::info('quest end:'.$url, __LINE__, __FILE__);
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
        $options = [
            'name' => 'Quest',
            'base' => 'Quest/Quest',
            'mode' => 'quest',
            'tipsLoading' => 'true',
            'id' => $quest->getQuestId(),
            'difficulty_id' => 'normal',
            'party_id' => '001',
            'unit_ids' => '1,2,3,4,5,6,7,10,13,14,17,44,83,93,94,163,164,165,170,171,172,173,177,178,179,181,182,184,189',
        ];
        $query = http_build_query($options);
        $url = '/quests/execute/'.$quest->getQuestId().'.json?'.$query;
        \app\helper\Logger::info('quest end:'.$url, __LINE__, __FILE__);
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
            return $quest;
        } else {
            \app\helper\Logger::warning('quest start failed', __LINE__, __FILE__);
            $quest->setSucceedStart(false);
            return $quest;
        }
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