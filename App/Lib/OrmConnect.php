<?php
namespace App\Lib;

use EasySwoole\Component\Singleton;
//配置orm数据连接
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\Db\Config;

Class OrmConnect{

    use Singleton;

    public function connect()
    {
        $conf = \Yaconf::get("mysql");
        $config = new Config();
        $config->setDatabase($conf['Database']);
        $config->setUser($conf['User']);
        $config->setPassword($conf['Password']);
        $config->setHost('192.168.137.53');
        DbManager::getInstance()->addConnection(new Connection($config));
    }


}
