<?php
namespace App\Lib;

use EasySwoole\Component\Singleton;

//配置orm数据连接
use EasySwoole\Redis\Redis;
use EasySwoole\Redis\Config\RedisConfig;

Class RedisConnect{

    use Singleton;

    public function connect(): ?Redis
    {
        $conf = \Yaconf::get("redis");
        try {
            //code...
            return new Redis(new RedisConfig([
                'host'      => $conf['host'],
                'port'      => $conf['port'],
                'serialize' => RedisConfig::SERIALIZE_NONE
            ]));
        } catch (\Exception $e) {
            throw $e;
        }
    
    }


}
