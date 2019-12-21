<?php

namespace app\adminapi\controller;

use think\Controller;

class Upload extends BaseApi
{
    // 单文件上传
    public function logo(){
        // 接收参数
        $params=input();
        // 参数检测
        if(!isset($params['type']) || empty($params['type'])){
            $this->fail("参数错误");
        }
        // 参数检测 type里面是否有指定的值
        if(!in_array($params['type'],['goods','category','brand'])){
            $params['type']='other';
        }
        // 处理数据图片上传
        $file=request()->file('logo');
        if(empty($file)){
            $this->fail("图片上传不能为空");
        }
        // 组织 文件路径
        $dir=ROOT_PATH.'public'.DS.'uploads'.DS.$params['type'];
        if(!is_dir($dir)) mkdir($dir);
        // 检测 文件并移动文件
        $info=$file->validate(['size'=>5*1024*1024,'ext'=>'jpg,png,gif,jpeg','type'=>'image/jpeg,image/png,image/gif'])->move($dir);
        // 判断文件是否上传成功
        if(empty($info)){
            // 把错误信息取出来 给他
            $this->fail($file->getError());
        }
        // 返回数据  成功的时候
        $logo='/uploads'.DS.$params['type'].DS.$info->getSaveName();
        $this->ok($logo);
    }
    // 多文件上传
    public function images(){
        // 接收参数
        $type=input('type','goods');
        // 参数检测
        if($type !== 'goods'){
            $type='other';
        }
        // 处理 数据
        $dir=ROOT_PATH.'public'.DS.'uploads'.DS.$type;
        if(!is_dir($dir)) mkdir($dir);
        // 文件上传
        $files=request()->file('images');
//        dump($files);die;
        if(empty($files) || !is_array($files)){
            $this->fail("请上传多个图片");
        }
        // 定义结果数据
        $res=[
            'success'=>[],
            'error'=>[],
        ];
        foreach ($files as $file){
            // 移动文件
            $info=$file->validate(['size'=>5*1024*1024,'ext'=>'jpg,png,gif,jpeg','type','type'=>'image/jpeg,image/png,image/gif'])->move($dir);
            if($info){
                // 证明上传成功
                $res['success'][]='/uploads'.DS.$type.DS.$info->getSaveName();
            }else{
                // 证明上传失败的时候
                $res['error'][]=[
                    'name'=>$file->getInfo('name'),
                    'msg'=>$file->getError()
                ];
            }
        }
        // 返回数据
        $this->ok($res);
    }
}
