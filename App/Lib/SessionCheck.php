<?php
namespace App\Lib;

use EasySwoole\Component\Singleton;
use EasySwoole\Session\Session;

Class SessionCheck{

    use Singleton;

    public function setCookie($request, $response)
    {
        $cookie = $request->getCookieParams('easy_session');
        if(empty($cookie)){
            $sid = Session::getInstance()->sessionId();
            $response->setCookie('easy_session',$sid);
        }else{
            Session::getInstance()->sessionId($cookie);
        }
        
    }

}
