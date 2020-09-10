<?php
namespace App\Models;

use EasySwoole\ORM\AbstractModel;

class AdminRole extends AbstractModel{

    /**
      * @var string 
    */
     protected $tableName = 'admin_role';

     public function myrole()
     {
        $this->belongsToMany(AdminPermission::class,'admin_role_permission','role_id','permission_id');
     }     
}
