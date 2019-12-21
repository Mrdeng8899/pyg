<?php

namespace app\common\model;

use think\Model;

class Spec extends Model
{
    // 定能够以sepc和spec_value 关联关系 一个spec名称有多个spec_value值
    public function spec_Value(){
        return $this->hasMany('SpecValue','spec_id','id');
    }
}
