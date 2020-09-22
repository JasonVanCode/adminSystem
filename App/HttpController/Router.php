<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/15
 * Time: 上午10:39
 */

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $this->setGlobalMode(true);
        $routeCollector->addGroup('/api',function (RouteCollector $collector){
            //登录的控制器路由
            $collector->post('/login', '/Api/LoginController/login');
            $collector->get('/menulist', '/Api/LoginController/getMenulist');
            //人员管理控制器路由
            $collector->get('/user', '/Api/UserController/getlist');
            $collector->post('/user/save', '/Api/UserController/save');
            $collector->post('/user/del', '/Api/UserController/del');
            //权限管理的权限
            $collector->get('/auth', '/Api/AuthController/getlist');
            $collector->get('/auth/getrolelist', '/Api/AuthController/getrolelist');
            $collector->get('/auth/getrolemenulist', '/Api/AuthController/getroleMenulist');
            $collector->post('/auth/save', '/Api/AuthController/save');

            //上传文件
            $collector->post('/file/upload', '/Api/UploadController/login');
            //退出登录
            $collector->post('/loginout', '/Api/LoginController/loginOut');
        });

    }
}