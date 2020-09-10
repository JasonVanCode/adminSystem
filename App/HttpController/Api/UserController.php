<?php
namespace App\HttpController\Api;

use App\Models\AdminUser as User;
use App\HttpController\Base;
use App\Lib\ValidateCheck;
use App\Models\AdminUserRole;

class UserController extends Base
{

    public function getlist()
    {
        $params = $this->request()->getRequestParam();
        $page = isset($params['page'])?$params['page']:1;
        $perpage = isset($params['perpage'])?$params['perpage']:10;
        $model  = User::create()
                ->alias('us')
                ->join('admin_user_role as ro','ro.user_id = us.user_id','LEFT')
                ->order('user_id')
                ->limit($perpage * ($page - 1), $perpage)
                ->withTotalCount()
                ->group('user_id')
                ->field(['us.user_id','username','password','realname','avatar','phone','email','sex','locked','ctime','group_concat(ro.role_id) as role_id']);
        $list = $model->all(null);
        // 总条数
        $total = $model->lastQueryResult()->getTotalCount();
        $finalresult = ['current_page'=>$page,'total'=>$total,'data'=>$list];
        return $this->writeJson(200, $finalresult,'获取数据成功');        
    }

    public function save()
    {
        $file = $this->request()->getUploadedFile('file');
        $form = $this->request()->getRequestParam()['form'];
        // 将json转数组
        $form = json_decode($form,true);
        //判断必填字段是否
        $vali = new ValidateCheck();
        $vali = $vali->validateRule('usersave');
        $res = $vali->validate($form);
        if(!$res){
            return $this->writeJson(200,'',$vali->getError()->__toString());
        }
        $head_img = '';
        if($file){
            $head_img = $this->savefile($file);
        }
        $user_id = $form['user_id'];
        $role_id = $form['role_id'];
        unset($form['user_id'],$form['role_id']);
        try {
            if($user_id){
                $form['avatar'] = $head_img?$head_img:$form['avatar'];
                $res = User::create()->update($form,['user_id',$user_id]);
            }else{
                $user_id = User::create($form);
            }
           
        } catch (\Exception $e) {
            return $this->writeJson(200,'','数据添加失败');
        }

        $this->handelUserRole($role_id,$user_id);
        return $this->writeJson(200,'','添加数据成功');
    }

    /**
     * 设置属性
     * @param $arr
     * @param $type 0 人员新增 1 人员编辑
     * @return bool
     * @throws Exception
     */

    public function handelUserRole($arr,$user_id): bool
    {
        try {
            $model = AdminUserRole::create();
            if($user_id){
                $model->destroy(['user_id'=>$user_id]);
            }
            foreach($arr as $v){
                $savedata[] = ['user_id' => $user_id,'role_id'=>$v];
            } 
            $model->saveAll($savedata,false);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function savefile($file)
    {
        $ext = ['png','jpg','jpeg'];
        $basedir = EASYSWOOLE_ROOT.'/Public/head/'.date('Y-m').'/';
        // //判断该文件夹是否存在
        if(!file_exists($basedir)){
            mkdir ($basedir,0777,true);
        }
        $mediatype =  $file->getClientMediaType();
        $suffix = explode('/',$mediatype)[1];
        if(!in_array($suffix,$ext)){
            return '';
        }
        $newname = md5(time() . mt_rand(1,1000000)).'.'.$suffix;
        $res = $file->moveTo($basedir . $newname);
        return $res?$basedir . $newname:'';
    }

    public function del()
    {
        $params = $this->request()->getRequestParam();
        $id = isset($params['id'])?$params['id']:null;
        try {    
            $res = $id?User::create()->destroy(['id' => $id]):false;
        } catch (\Exception $e) {
            return $this->writeJson(500,[],'删除数据失败');
        }
        return $this->writeJson($res?200:500,[],$res?'删除成功':'删除失败');
    }


}