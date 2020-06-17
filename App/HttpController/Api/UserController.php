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
        // $head_img = $this->savefile($file);
        $form = $this->request()->getRequestParam()['form'];
        //将json转数组
        $form = json_decode($form,true);
        //判断必填字段是否
        $vali = new ValidateCheck();
        $vali = $vali->validateRule('usersave');
        $res = $vali->validate($form);
        if(!$res){
            return $this->writeJson(500,'',$vali->getError()->__toString());
        }
        User::create()->data($form,false)->save();  
        var_dump(json_decode($form,true));
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
        return $res?$newname:$this->writeJson(500,'','文件上传失败');
    }


    public function getedit()
    {
        $params = $this->request()->getRequestParam();
        $id = isset($params['id'])?$params['id']:null;
        try {
            $data = $id?User::create()->where('id',$id)->get(1):[];
        } catch (\Exception $e) {
            return $this->writeJson(500,[],'获取数据失败');
        }
        return $this->writeJson(200,$data,'获取数据成功');
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
        return $this->writeJson(200,[],$res?'删除成功':'删除失败');
    }


}