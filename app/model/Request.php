<?php

namespace app\model;

use Guzzle\Http\Client;

class Request {
    
    public static function getClient($APP_ID_1, $APP_ID_2)
    {
        $client = new Client('http://toto.hekk.org/');
        $client->setDefaultOption('headers', [
            'User-Agent'        => 'toto/1.0.3 CFNetwork/672.1.13 Darwin/14.0.0',
            'APP_ID_1'          => $APP_ID_1,
            'Accept-Language'   => 'ja-jp',
            'Accept-Encoding'   => 'gzip, deflate',
            'Device'            => 'ios',
            'APP_ID_2'          => $APP_ID_2,
            'Accept'            => 'application/json',
            'Content-Type'      => 'application/x-www-form-urlencoded',
            'DEVICE_INFO'       => 'iPhone4,1:::iPhone OS 7.0.6',
            'Connection'        => 'keep-alive',
            'AppVersion'        => '4',
        ]);
        $client->getConfig()->set('request.params', array(
            'redirect.strict' => true,
            'redirect.disable' => true
        ));
        return $client;
    }
}