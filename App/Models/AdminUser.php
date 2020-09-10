<?php
namespace App\Models;

use EasySwoole\ORM\AbstractModel;

class AdminUser extends AbstractModel{

    /**
      * @var string 
    */
     protected $tableName = 'admin_user';
    
     public function roles()
     {
        $this->belongsToMany(AdminRole::class,'admin_user_role','user_id','role_id');
     }
     
}
