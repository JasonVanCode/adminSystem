<?php
namespace App\HttpController\Api;

use App\Models\User;
use App\HttpController\Base;
use App\Lib\ValidateCheck;

class UserController extends Base
{
    public function getlist()
    {
        $params = $this->request()->getRequestParam();
        $page = isset($params['page'])?$params['page']:1;
        $perpage = isset($params['perpage'])?$params['perpage']:10;
        $model  = User::create()->order('id')->limit($perpage * ($page - 1), $perpage)->withTotalCount();
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
            return $this->writeJson(500,'',$vali->getError()->__toString());
        }
        try {
            $head_img = $this->savefile($file);
            User::create()->data(['name'=>$form['name'],'passwd'=>md5($form['passwd']),'head_img'=>$head_img,'province'=>$form['provinces'][0],'city'=>$form['provinces'][1],'address'=>$form['address']],false)->save(); 
            return $this->writeJson(200,'','添加数据成功');
        } catch (\Exception $e) {
            return $this->writeJson(500,'','数据添加失败');
        }
    }

    public function savefile($file)
    {
        $ext = ['png','jpg','jpeg'];
        $basedir = EASYSWOOLE_ROOT.'/Public/head/'.date('Y').'/';
        // //判断该文件夹是否存在
        if(!file_exists($basedir)){
            mkdir ($basedir,0777,true);
        }
        $mediatype =  $file->getClientMediaType();
        if(!$mediatype){
            return $this->writeJson(500,'','文件上传的格式错误');
        }
        $suffix = explode('/',$mediatype)[1];
        if(!in_array($suffix,$ext)){
            return $this->writeJson(500,'','文件上传的格式错误');
        }
        $newname = md5(time() . mt_rand(1,1000000)).'.'.$suffix;
        $res = $file->moveTo($basedir . $newname);
        return $res?$basedir . $newname:$this->writeJson(500,'','文件上传失败');
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