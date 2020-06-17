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
        return new Redis(new RedisConfig([
            'host'      => $conf['host'],
            'port'      => $conf['port'],
            // 'auth'      => $conf['auth'],
            'serialize' => RedisConfig::SERIALIZE_NONE
        ]));
    }


}
