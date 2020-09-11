<?php
namespace EasySwoole\EasySwoole;

use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use App\Lib\OrmConnect;
use App\Lib\SessionCheck;
use EasySwoole\Session\Session;
use EasySwoole\Session\SessionFileHandler;

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
        // TODO: Implement mainServerCreate() method.
         //可以自己实现一个标准的session handler
         $handler = new SessionFileHandler(EASYSWOOLE_TEMP_DIR);
         //表示cookie name   还有save path
         Session::getInstance($handler,'easy_session','session_dir');
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