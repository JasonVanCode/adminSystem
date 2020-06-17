<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use App\Lib\RedisConnect;

class Base extends Controller
{
    protected function onRequest(?string $action): ?bool
    {
        return true;
        if($action == 'login'){
            return true;
        }
        //判断用户的登录状态
        $token = $this->request()->getCookieParams('login_token');
        if(!$token){
            $this->writeJson(401,null,'请先登录');
        }
        $redis =  RedisConnect::getInstance();
        $user_id = $redis->get('$token');
        if( !$user_id ){
            $this->writeJson(401,null,'登录超时，请重新登录');
        }
        return true;
    }



}