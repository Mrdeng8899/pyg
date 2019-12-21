<?php

namespace app\adminapi\controller;

use think\Controller;
use think\Request;

class Type extends BaseApi
{
    /*
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 查询所有数据
        $info=\app\common\model\Type::select();
        $this->ok($info);
    }

    /*
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 接收参数
        $params=input();
        // 定义检测参数
        $rule=[
          'type_name|模型名称','require',
          'spec|规格'=>'require|array',
          'attr|属性'=>'require|array',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->fail($validate,400);
        }
        // 添加数据有四个操作 如果成功 要么都成功要么都失败 此时使用事务
        // 开启事务
        \think\Db::startTrans();
        try {
            // 处理数据  添加type数据
            $type = \app\common\model\Type::create($params, true);
            // 检测商品规格信息
            foreach ($params['spec'] as $k => $v) {
                // 如果规格名称为空 则删除整条当前整条数据
                if (empty($v['name'])) {
                    unset($params['spec'][$k]);
                    continue;
                }
                // 如果规格值 不是数据 则删除整条数据
                if (!is_array($v['value'])) {
                    unset($params['spec'][$k]);
                    continue;
                }
                // 规格里面可能出现 空值 此时需要foreach 遍历
                foreach ($v['value'] as $key => $value) {
                    if (empty($value)) {
                        unset($params['spec'][$k]['value'][$key]);
                        continue;
                    }
                }
                    // 如果整个值数组为空数组 删除整条数据
                    if (empty($params['spec'][$k]['value'])) {
                        unset($params['spec'][$k]);
                        continue;
                    }
                }
            // 添加商品规格名称
            $spec_data = [];
            foreach ($params['spec'] as $k => $v) {
                $spec_data[] = [
                    'type_id' => $type['id'],
                    'spec_name' => $v['name'],
                    'sort' => $v['sort'],
                ];
            }
                $spec_model=new \app\common\model\Spec();
                $spec_res=$spec_model->saveAll($spec_data);


                // 添加 商品规格值
                $spec_value_data=[];
                foreach($params['spec'] as $k=>$v){
                    foreach ($v['value'] as $value){
                        $spec_value_data=[
                          'spec_id'=>$spec_res[$k]['id'],
                          'spec_value'=>$value,
                          'type_id'=>$type['id'],
                        ];
                    }
                }
                $spec_value_model= new \app\common\model\SpecValue();
                $spec_value_model->saveAll($spec_value_data);


                //  检测商品属性信息
            foreach($params['attr'] as $k=>$v){
                // 如果属性名称为空 则删除整条数据
                if(empty($v['name'])){
                    unset($params['attr'][$k]);
                    continue;
                }
                // 如果属性值不是数组 则设置为空数组
                if(!is_array($v['value'])){
                    $params['attr'][$k]['value']=[];
                    continue;
                }

                // 如果属性值 数组中有空值 则删除空值
                foreach ($v['value'] as $key=>$value){
                    if(empty($value)){
                        unset($params['attr'][$k]['value'][$key]);
                        continue;
                    }
                }
            }
            // 添加商品属性信息
            $attr_data=[];
            // 添加商品属性信息
            foreach($params['attr'] as $k=>$v){
                $attr_data[]=[
                  'attr_name'=>$v['name'],
                  'type_id'=>$type['id'],
                  'attr_values'=>implode(',',$v['value']),
                  'sort'=>$v['sort'],
                ];
            }

            $attr_model=new \app\common\model\Attribute();
            $attr_model->saveAll($attr_data);

            // 设置 提交事务
            \think\Db::commit();
            $this->ok();
        }catch(\Exception $e){
            // 事务回滚
            \think\Db::rollback();
            $this->fail("操作失败");
        }
    }

    /*
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        // 嵌套关联
        $info=\app\common\model\Type::with('specs,specs.spec_values,attrs')->find();
        $this->ok($info);
    }

    /*
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /*
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        // 数据更新  接收修改的参数
        $params=input();
        // 参数检测
        $rule=[
            'type_name|模型名称','require',
            'spec|规格','require|array',
            'attr|属性','require|array',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->fail($validate,400);
        }
        // 修改数据 4个修改数据操作
        // 使用事务
        // 开启事务
        \think\Db::startTrans();
        try{
           // 事务操作
            // 处理数据 修改type数据
            \app\common\model\Type::update($params,[],true);
            foreach($params['spec'] as $k=>$v){
                // 如果规格名称为空 就删除整条数据
                if(empty($v['name'])){
                    unset($params['spec'][$k]);
                    continue;
                }
                // 判断如果他的value值不是数组 可以直接删除
                if(!is_array($v['value'])){
                    unset($params['spec'][$k]);
                    continue;
                }
                // 判断规格值是不是有空值的数值
                foreach($v['value'] as $key=>$value){
                    if(empty($value)){
                        unset($params['spec'][$k]['value'][$key]);
                        continue;
                    }
                }
                // 判断 如果规格数组为空数组 删除整条数据
                if(empty($params['spec'][$k]['value'])){
                    unset($params['spec'][$k]);
                    continue;
                }
            }
            // 添加商品规格名称
            // 先删除原来的 再添加新的
            \app\common\model\Spec::destroy(['type_id'=>$id]);
            $spec_data=[];
            foreach($params['spec'] as $k=>$v){
                $spec_data[]=[
                    'type_id'=>$id,
                    'spec_name'=>$v['name'],
                    'sort'=>$v['sort'],
                ];
            }
            $spec_model= new \app\common\model\Spec();
            $spec_res=$spec_model->saveAll($spec_data);


            // 添加商品规格值
            // 先删除原来 再添加新的
            \app\common\model\SpecValue::destroy(['type_id'=>$id]);
            $spec_value_data=[];
            foreach($params['spec'] as $k=>$v){

                foreach ($v['value'] as $value){
                    $spec_value_data[]=[
                        'spec_id'=>$spec_res[$k]['id'],
                        'spec_value'=>$value,
                        'type_id'=>$id,
                    ];
                }
            }
            $spec_value_model=new \app\common\model\SpecValue();
            $spec_value_model->saveAll($spec_value_data);

            // 检测商品属性信息
            foreach($params['attr'] as $k=>$v){
                // 如果属性的名字为空 则删除整数据
                if(empty($v['name'])){
                    unset($params['attr'][$k]);
                    continue;
                }
                // 如果属性值不是一个数组 则给他设置一个数组
                if(!is_array($v['value'])){
                    $params['attr'][$k]['value']=[];
                    continue;
                }
                // 如果属性数组中有空值 则删除空值
                foreach($v['value'] as $key=>$value){
                    if(empty($value)){
                        unset($params['attr'][$k]['value'][$key]);
                        continue;
                    }
                }
            }
            // 先删除商品属性的信息
            // 先删除原来的 然后 添加新的
            \app\common\model\Attribute::destroy(['type_id'=>$id]);
            $attr_data=[];
            foreach($params['attr'] as $k=>$v){
                $attr_data[]=[
                  'attr_name'=>$v['name'],
                  'type_id'=>$id,
                  'attr_values'=>implode(',',$v['value']),
                  'sort'=>$v['sort'],
                ];
            }
            $attr_model=new \app\common\model\Attribute();
            $attr_model->saveAll($attr_data);


            //提交事务
            \think\Db::commit();
            $this->ok();
        }catch (\Exception $e){
            // 事务回滚
            \think\Db::rollback();
            $this->fail("操作失败");
        }
    }

    /*
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        // 删除4张表 都要删除
        // 开启事务
        \think\Db::startTrans();
        try{
            // 删除type 表
            \app\common\model\Type::destroy($id);
            // 删除规格名称表 数据
            \app\common\model\Spec::destroy(['type_id'=>$id]);
            // 删除规格值表 数据
            \app\common\model\SpecValue::destroy(['type_id'=>$id]);
            // 删除属性表的数据
            \app\common\model\Attribute::destroy(['type_id'=>$id]);
            // 提交事务
            \think\Db::commit();
            $this->ok();
        }catch(\Exception $e){
            // 回滚事务
            \think\Db::rollback();
            $this->fail("操作失败");
        }
    }
}
