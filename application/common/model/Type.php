<?php

namespace app\common\model;

use think\Model;

class Type extends Model
{
    // 定义类型 规格关联 一个类型下有多个规格
    public function specs(){
        return $this->hasMany('Spec','type_id','id');
    }
    // 定义 type 模型 和属性 attribute的关联 一个type有多个属性attribute
    public function attrs(){
        return $this->hasMany('Attribute','type','id');
    }
}
