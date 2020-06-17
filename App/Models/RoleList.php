<?php
namespace App\Models;

use EasySwoole\ORM\AbstractModel;

class RoleList extends AbstractModel{

    /**
      * @var string 
    */
     protected $tableName = 'role_list';

     public function mymenu()
     {
        return $this->belongsToMany(MenuList::class,'role_menu','role_id','menu_id');
     }

     
}



