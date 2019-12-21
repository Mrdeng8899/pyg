<?php

namespace app\common\model;

use think\Model;

class User extends Model
{
    // 修改器
    public function setNickNameAttr($value){
        return urlencode($value);
    }
    // 获取器
    public function getNickNameAttr($value){
        return urldecode($value);
    }
}
