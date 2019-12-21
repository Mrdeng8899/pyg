<?php

namespace app\common\model;

use think\Model;

class Goods extends Model
{
    // 定义商品 商品模型关联 一个商品属于一个商品模型
    public function typeBind(){
        return $this->belongsTo('Type','type_id','id')->bind('type_name');
    }
    public function type(){
        return $this->belongsTo('Type','type_id','id');
    }
    // 定义商品 商品品牌关联 一个商品属于一个商品品牌
    public function brandBind(){
        return $this->belongsTo('Brand','brand_id','id')->bind(['bind_name'=>'name']);
    }
    public function brand(){
        return $this->belongsTo('Brand','brand_id','id');
    }
    //定义商品 商品分类 一个商品属于一个商品分类
    public function categoryBind(){
        return $this->belongsTo('Category','cate_id','id')->bind('cate_name');
    }
    public function category(){
        return $this->belongsTo('Category','cate_id','id');
    }
    // 定义商品 相册关联 一个商品有多个相册图片
    public function goodsImages(){
        return $this->hasMany('GoodsImages','goods_id','id');
    }
    // 定义商品 规格商品sku关联 一个商品商铺有多个sku
    public function specGoods(){
        return $this->hasMany('SpecGoods','goods_id','id');
    }


    // 获取器 对goods_attr字段进行转换
    public function getGoodsAttrAttr($value){
        return $value ? json_decode($value,true) : [];
    }
    protected static function init()
     {
         try{
             //实例化ES工具类
             $es = new \tools\es\MyElasticsearch();
             //设置新增回调
             self::afterInsert(function($goods)use($es){
                 //添加文档
                 $doc = $goods->visible(['id', 'goods_name', 'goods_desc', 'goods_price'])->toArray();
                 $doc['cate_name'] = $goods->category->cate_name;
                 $es->add_doc($goods->id, $doc, 'goods_index', 'goods_type');
             });
             //设置更新回调
             self::afterUpdate(function($goods)use($es){
                 //修改文档
                 $doc = $goods->visible(['id', 'goods_name', 'goods_desc', 'goods_price', 'cate_name'])->toArray();
                 $doc['cate_name'] = $goods->category->cate_name;
                 $body = ['doc' => $doc];
                 $es->update_doc($goods->id, 'goods_index', 'goods_type', $body);
             });
             //设置删除回调
             self::afterDelete(function($goods)use($es){
                 //删除文档
                 $es->delete_doc($goods->id, 'goods_index', 'goods_type');
             });
         }catch(\Exception $e){

         }

    }
}
