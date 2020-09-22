<?php
namespace App\HttpController\Api;

use App\Models\AdminUser as User;
use App\HttpController\Base;
use App\Lib\ValidateCheck;
use App\Lib\RedisConnect;
use App\Models\AdminRole;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\DbManager;

class LoginController extends Base
{
    public function login()
    { 
        $params = $this->request()->getRequestParam();
        //请求字段判断
        $vali = new ValidateCheck();
        $vali = $vali->validateRule('login');
        $res = $vali->validate($params);
        if(!$res){
            return $this->writeJson('200','',$vali->getError()->__toString());
        }
        $user = $this->usercheck($params);
        if(!$user){
            return false;
        }
        $uniquestr = $this->tokenSave($user);
        return $this->writeJson(200,['token'=>$uniquestr],'登录成功');
    }

    public function usercheck($params)
    {
        try {
            $user = User::create()->where([
                'username'=>$params['name'],
                'password'=>$params['password']
            ])->get(1);
        } catch (\Exception $e) {
             $this->writeJson(200,'','数据库连接失败');
             return false;
        }
        if(!$user){
            $this->writeJson(200,'','该账户不存在');
            return false;
        }
        return $user;
    }


    public function tokenSave($user)
    {
        try{
            $redis = RedisConnect::getInstance()->connect();
        }catch(\Exception $e){
            return $this->writeJson(200,[],'redis连接错误');
        }
        //生成唯一的32位字符串
        $uniquestr = md5(date('Y-m-d H:i:s').mt_rand(0,1000));
        $redis->set($uniquestr,json_encode($user),3600);
        return $uniquestr;
    }

    public function getMenulist()
    {
        //请求到这个方法说明已经登录到页面当中了
        $userinfo = $this->userinfo;
        $queryBuild = new QueryBuilder();
        $queryBuild->raw("
                select a.role_id,c.* from admin_user_role as a
                left join admin_role_permission as b on a.role_id = b.role_id
                left join admin_permission as c on c.permission_id = b.permission_id
                where a.user_id = ?
                GROUP BY b.permission_id
                ORDER BY c.orders", [1]);

        $data = DbManager::getInstance()->query($queryBuild, true, 'default');
        if(!$data || !$data->toArray()['result']){
                return $this->writeJson(500,null,'该账号无权登录');
        }
        $menulist = $data->toArray()['result'];
        $menuTree = $this->getMenuTree( $menulist , 0);
        $menuTree = $this->sortMenu($menuTree);
        return $this->writeJson(200,$menuTree,'获取数据成功');
    }

    public function getMenuTree($data, $pId)
    {
        $tree = array();
        foreach($data as $v)
        {
            if($v['pid'] == $pId )
            {    //父亲找到儿子
                $v['subs'] = $this->getMenuTree($data, $v['permission_id']);
                $pre_data = ['id'=>$v['permission_id'],'index'=>$v['uri'],'title'=>$v['name'],'sort'=>$v['orders']];
                if($v['subs']){
                    $pre_data['subs'] = $v['subs'];
                }
                if($v['icon']){
                    $pre_data['icon'] = $v['icon'];
                }
                $tree[] = $pre_data;
            }
        }
        return $tree;
    }

    //排序
    public function sortMenu($dataArr)
    {
        $timeKey =  array_column( $dataArr, 'sort'); //取出数组中status的一列，返回一维数组
        array_multisort($timeKey, SORT_ASC, $dataArr);//排序，根据$status 排序
        return $dataArr;
    }


    public function loginOut()
    {
        try{
            $redis = RedisConnect::getInstance()->connect();
            $redis->del($this->token);
            return $this->writeJson(200,null,'退出成功');
        }catch(\Exception $e){
            return $this->writeJson(500,null,'redis连接失败');
        }
    }

}