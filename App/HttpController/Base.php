<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use App\Lib\RedisConnect;
use App\Models\AdminLog;

class Base extends Controller
{

    protected function onRequest(?string $action): ?bool
    {
        if($action == 'login'){
            $server_list = $this->request()->getServerParams();
            return $this->loginLog($server_list);
        }
        return true;
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
        $server_list = $this->request()->getServerParams();
        return true;
    }

    public function loginLog($server_list)
    {
        $res = AdminLog::create()->data([
            'description'=>'登录操作',
            'username'=>'admin',
            'start_time'=>date('Y-m-d H:i:s',$server_list['request_time']),
            'method'=>$server_list['request_method'],
            'ip'=>$server_list['remote_addr'],
            'uri'=>$server_list['request_uri'],
            'url'=>$server_list['path_info']
            ]);
        return $res?true:false;
    }


}