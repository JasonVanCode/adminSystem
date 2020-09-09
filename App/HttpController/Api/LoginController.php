<?php
namespace App\HttpController\Api;

use App\Models\User;
use App\HttpController\Base;
use App\Lib\ValidateCheck;
use App\Lib\RedisConnect;
use App\Models\RoleList;


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
            return $this->writeJson('500','',$vali->getError()->__toString());
        }
        $user = $this->usercheck($params);
        if(!$user){
            return false;
        }
        $uniquestr = $this->savesession($user->id);
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


    public function savesession($id)
    {
        try{
            $redis = RedisConnect::getInstance()->connect();
        }catch(\Exception $e){
            return $this->writeJson(500,'','redis连接错误');
        }
        //生成唯一的32位字符串
        $uniquestr = md5(date('Y-m-d H:i:s').mt_rand(0,1000));
        $redis->set($uniquestr,$id,3600);
        return $uniquestr;
    }

    public function getMenulist()
    {
        $params = $this->request()->getRequestParam();
        if(empty( $params['id'] ) || empty( $params['role_id'])){
            return $this->writeJson(200,null,'必填参数缺失');
        }
        $data = RoleList::create()->with(['mymenu'])->where(['id'=>1])->get(1);
        if(!$data){
            return $this->writeJson(500,null,'没有该账号信息');
        }
        //判断该账号下面的权限列表的信息
        if(!$data->mymenu){
            return $this->writeJson(500,null,'该账户没有任何权限');
        }
        $menulist = $data->mymenu;
        $menuTree = $this->getMenuTree( $menulist , 0);
        $menuTree = $this->sortMenu($menuTree);
        return $this->writeJson(200,$menuTree,'获取数据成功');
    }

    public function getMenuTree($data, $pId)
    {
        $tree = array();
        foreach($data as $k => $v)
        {
            if($v->pid == $pId )
            {        //父亲找到儿子
                $v->subs = $this->getMenuTree($data, $v->id);
                $pre_data = ['id'=>$v->id,'index'=>$v->index,'title'=>$v->title,'sort'=>$v->sort];
                if($v->subs){
                    $pre_data['subs'] = $v->subs;
                }
                if($v->icon){
                    $pre_data['icon'] = $v->icon;
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



}