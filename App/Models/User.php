<?php
namespace App\Models;

use EasySwoole\ORM\AbstractModel;

class User extends AbstractModel{

    /**
      * @var string 
    */
     protected $tableName = 'user';

     public function myrole()
     {
        return $this->hasOne(RoleList::class,null,'id','user_id');
     }


}



