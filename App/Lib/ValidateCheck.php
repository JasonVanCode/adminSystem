<?php
namespace App\Lib;
use EasySwoole\Validate\Validate;

Class ValidateCheck{

    public function validateRule(?string $action): ?Validate
    {
        $v = new Validate();
        switch ($action){
            case 'login':{
                $v->addColumn('name','登录名')->required('登录名不能为空')->notEmpty('不能为空');
                $v->addColumn('password','登录密码')->required('密码不能为空')->notEmpty('不能为空');
                break;
            }
            case 'usersave':{
                $v->addColumn('name','用户名')->required('用户名不能为空')->notEmpty('用户名不能为空');
                $v->addColumn('passwd','用户密码')->required('用户密码不能为空')->notEmpty('用户密码不能为空');
                $v->addColumn('provinces','省市')->required('省市不能为空')->notEmpty('省市不能为空');
                break;
            }
        }
        return $v;
    }

}
