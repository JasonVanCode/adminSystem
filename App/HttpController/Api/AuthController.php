<?php
namespace App\HttpController\Api;

use App\HttpController\Base;
use App\Models\AdminRole;
use App\Models\AdminPermission;
use App\Models\AdminRolePermission;

class AuthController extends Base
{
   public function getlist()
   {
        $menulist =  AdminPermission::create()->all(null);
        if(!$menulist){
            return  $this->writeJson(200,['status'=>'error'],'暂无菜单数据');
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
       foreach($data as $v)
       {
           if($v->pid == $pId )
           {        //父亲找到儿子
               $v->children = $this->getMenuTree($data, $v->permission_id);
               $pre_data = ['id'=>$v->permission_id,'label'=>$v->name,'sort'=>$v->orders];
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
            $result = AdminRolePermission::create()->where(['role_id' => $role_id])->column('permission_id');
        }
        return $this->writeJson(200,$result,'获取数据成功');
   }

   public function save()
   {
        $params = $this->request()->getRequestParam();
        if(!$params['role_id'] || empty($params['menulist'])){
            return $this->writeJson(200,['status'=>'fail'],'请选择角色、选择要分配的菜单');
        }
        $data = [];
        foreach($params['menulist'] as $v){
            $data[] = ['permission_id'=>$v,'role_id'=>$params['role_id']];
        }
        //不管之前角色有没有分配菜单，先清该角色id的数据
        try {
            AdminRolePermission::create()->destroy(['role_id'=>$params['role_id']]);
            AdminRolePermission::create()->saveAll($data,false);
            return $this->writeJson(200,['status'=>'success'],'操作成功');
        } catch (\Exception $e) {
            return $this->writeJson(200,['status'=>'error'],'权限分配失败');
        }
   }


}