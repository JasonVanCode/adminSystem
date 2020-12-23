<?php
namespace EasySwoole\EasySwoole;

use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use App\Lib\OrmConnect;
use EasySwoole\Component\Process\Manager;
use App\Process\TestProcess;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        //配置orm
        OrmConnect::getInstance()->connect();
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        $processConfig= new \EasySwoole\Component\Process\Config();
        $processConfig->setProcessName('testProcess');//设置进程名称
        $processConfig->setProcessGroup('Test');//设置进程组
        $processConfig->setArg(['a'=>123]);//传参
        $processConfig->setRedirectStdinStdout(false);//是否重定向标准io
        $processConfig->setPipeType($processConfig::PIPE_TYPE_SOCK_DGRAM);//设置管道类型
        $processConfig->setEnableCoroutine(true);//是否自动开启协程
        $processConfig->setMaxExitWaitTime(3);//最大退出等待时间
        Manager::getInstance()->addProcess(new TestProcess($processConfig));
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        $response->withHeader('Access-Control-Allow-Origin', '*');//允许所有跨域
        $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->withHeader('Access-Control-Allow-Credentials', 'true');
        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $response->withHeader('Content-type', 'application/json;charset=utf-8');
        if ($request->getMethod() === 'OPTIONS') {
            $response->withStatus(200);
            return false;
        }
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}