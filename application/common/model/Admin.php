<?php

namespace app\common\model;

use think\Model;

class Admin extends Model
{
    // 定义关联关系 一个管理员有一份档案 id uid id 是管理员主键
    public function profile(){
        return $this->hasOne('Profile','uid','id');
    }
    public function profileBind(){
        return $this->hasOne('Profile','uid','id')->bind('idnum,card');
    }
    public function roleBind(){
        return $this->belongsTo('Role','role_id','id')->bind('role_name');
    }

}
