<?php
namespace App\HttpController\Api;

use App\HttpController\Base;
use App\Models\AdminRole;

class AuthController extends Base
{
   public function getlist()
   {
        $menulist =  MenuList::create()->all(null);
        if(!$menulist){
            return  $this->writeJson(200,[],'暂无菜单数据');
        }
        $menuTree = $this->getMenuTree( $menulist , 0);
        $menuTree = $this->sortMenu($menuTree);
        return $this->writeJson(200,$menuTree,'获取数据成功');
   }

   public function getrolelist()
   {
       $data = AdminRole::create()->all(null);
       return $this->writeJson(200,$data,'获取数据成功');
   }

   public function getMenuTree($data, $pId)
   {
       $tree = array();
       foreach($data as $k => $v)
       {
           if($v->pid == $pId )
           {        //父亲找到儿子
               $v->children = $this->getMenuTree($data, $v->id);
               $pre_data = ['id'=>$v->id,'label'=>$v->title,'sort'=>$v->sort];
               if($v->children){
                   $pre_data['children'] = $v->children;
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

   public function getroleMenulist()
   {
        $params = $this->request()->getRequestParam();
        $role_id = isset($params['role_id'])?$params['role_id']:null;
        $result = [];
        if($role_id){
            $result = RoleMenu::create()->where(['role_id' => $role_id])->column('menu_id');
        }
        return $this->writeJson(200,$result,'获取数据成功');
   }

   public function save()
   {
        $params = $this->request()->getRequestParam();
        if(!$params['role_id'] || empty($params['menulist'])){
            return $this->writeJson(500,'','请选择角色、选择要分配的菜单');
        }
        //判断角色是否存在
         $isexists = RoleList::create()->get(['id'=>$params['role_id']]);
        if(!$isexists){
            return $this->writeJson(500,'','该角色不存在');
        }
        $data = [];
        foreach($params['menulist'] as $v){
            $data[] = ['menu_id'=>$v,'role_id'=>$params['role_id']];
        }
        //不管之前角色有没有分配菜单，先清该角色id的数据
        try {
            RoleMenu::create()->destroy(['role_id'=>$params['role_id']]);
            $res = RoleMenu::create()->saveAll($data,false);
            return $this->writeJson($res?200:500,null,$res?'获取数据成功':'权限分配失败');
        } catch (\Exception $e) {
            return $this->writeJson(500,'','权限分配失败');
        }
   }


}