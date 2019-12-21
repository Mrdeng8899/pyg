<?php

namespace app\adminapi\controller;

use think\Controller;
use think\Request;

class Goods extends BaseApi
{
    /*
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 搜索＋分页
        // 接收参数
        $params=input();
        // 判断搜索条件
        $where=[];
        if(!empty($params['keyword'])){
            $keyword=$params['keyword'];
            $where['goods_name']=["like","%{$keyword}%"];
        }
        $res= \app\common\model\Goods::with('type_bind,brand_bind,category_bind')->where($where)->paginate(10);
        $this->ok($res);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 接收参数
        $params=input();
        // 要检测的参数
        $rule=[
          'goods_name|商品名称'=>'require',
          'goods_price|商品的价格'=>'require|float|gt:0',
          'goods_number|商品的库存'=>'require|integer|gt:0',
          'goods_logo|商品的logo'=>'require',
          'goods_images|商品相册'=>'require|array',
          'item|规格值'=>'require|array',
          'attr|属性值'=>'require|array',
          'type_id|商品模型'=>'require',
          'brand_id|商品品牌'=>'require',
          'cate_id|商品分类'=>'require',
        ];
        /*$params = [
           'goods_name' => 'iphone X',
           'goods_price' => '8900',
           'goods_introduce' => 'iphone iphonex',
           'goods_logo' => '/uploads/goods/20190101/afdngrijskfsfa.jpg',
           'goods_images' => [
               '/uploads/goods/20190101/dfsssadsadsada.jpg',
               '/uploads/goods/20190101/adsafasdadsads.jpg',
               '/uploads/goods/20190101/dsafadsadsaasd.jpg',
           ],
           'cate_id' => '72',
           'brand_id' => '3',
           'type_id' => '16',
           'item' => [
               '18_21' => [
                   'value_ids'=>'18_21',
                   'value_names'=>'颜色：黑色；内存：64G',
                   'price'=>'8900.00',
                   'cost_price'=>'5000.00',
                   'store_count'=>100
               ],
               '18_22' => [
                   'value_ids'=>'18_22',
                   'value_names'=>'颜色：黑色；内存：128G',
                   'price'=>'9000.00',
                   'cost_price'=>'5000.00',
                   'store_count'=>50
               ]
           ],
           'attr' => [
               '7' => ['id'=>'7', 'attr_name'=>'毛重', 'attr_value'=>'150g'],
               '8' => ['id'=>'8', 'attr_name'=>'产地', 'attr_value'=>'国产'],
           ]
       ];*/
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->fail($validate,400);
        }
        // 开启事务
        \think\Db::startTrans();
        // 使用事务
        try{
            // 商品表数据
                // logo图片生成缩略图
            if(is_file('.'.$params['goods_logo'])){

                $goods_logo=dirname($params['goods_logo']).DS.'thumb_'.basename($params['goods_logo']);
                \think\Image::open('.'.$params['goods_logo'])->thumb(200,240)->save('.'.$goods_logo);

                $params['goods_logo']=$goods_logo;
            }else{
                $this->fail("商品logo图片不存在");
            }
            // 将商品属性转换成json格式的字符串
            $params['goods_attr']=json_encode($params['attr'],JSON_UNESCAPED_UNICODE);
            // 添加商品数据
            $goods= \app\common\model\Goods::create($params,true);
            // 添加商品表数据
            $goods_images_data=[];
            foreach($params['goods_images'] as $k=>$v){

                if(!is_file('.'.$v)){
                        continue;
                    }
                    # $v 是一个图片地址 需要生成两张不同尺寸的缩略图 组装成一条数据
                    $pics_big=dirname($v).DS.'thumb_800_'.basename($v);
                    $pics_sma=dirname($v).DS.'thumb_400_'.basename($v);
                    // 生成缩略图
                    $image=\think\Image::open('.'.$v);
                    $image->thumb(800,800)->save('.'.$pics_big);
                    $image->thumb(400,400)->save('.'.$pics_sma);
                    $goods_images_data[]=[
                        'goods_id'=>$goods['id'],
                        'pics_big'=>$pics_big,
                        'pics_sma'=>$pics_sma,
                    ];
                }
                // 实例化存储图片的类
                $goods_images=new \app\common\model\GoodsImages();
                // 保存图片的数据
                $goods_images->saveAll($goods_images_data);
                // 规格商品表数据 sku表
                $spec_goods_data=[];
                foreach($params['item'] as $v){
                    $v['goods_id']=$goods['id'];
                    $spec_goods_data=$v;
                }
                // 实例化SpecGoods 保存数据
            $spec_goods_model=new \app\common\model\SpecGoods();
            $spec_goods_model->saveAll($spec_goods_data);
              //提交事务
            \think\Db::commit();
            // 返回数据
            $info=\app\common\model\Goods::with('type_bind,brand_bind,category_bind')->find($goods['id']);
            $this->ok($info);
        }catch(\Exception $e){
            // 事务回滚
            \think\Db::rollback();
           $line=$e->getFile();
           $msg=$e->getMessage();
           $file=$e->getLine();
            $this->fail($line.','.$file.','.$msg);
        }



    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        // 查询商品一条数据 相册图片 规格商品sku 所属分类 所属品牌
        $info= \app\common\model\Goods::with('goods_images,spec_goods,category,brand')->find($id);
        $info['type']=\app\common\model\Type::with('attrs,specs,specs.spec_values')->find($info['type_id']);
        $this->ok($info);
        $this->ok($info);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        // 编辑页面 显示数据
        // 先查询商品相关数据 相册图片 规格图片sku 所属分类 所属品牌
        $goods=\app\common\model\Goods::with('goods_images,category,brand,spec_goods')->find();
        // 关联模型查询 不允许多个嵌套关联 只能有一个生效 查询所属模型及规格属性
        $goods['type']=\app\common\model\Type::with('attrs,specs,specs.spec_values')->find($goods['type_id']);
        // 查询所有模型列表type
        $type=\app\common\model\Type::select();
        // 查询商品分类 用户三级联动的三个下拉列表显示
        // 查询所有的第一级分类
        $cate_one=\app\common\model\Category::where('pid',0)->select();
        // 找到 商品所属的一级分类id  和二级分类id
        $pid_path=$goods['category']['pid_path'];
        // 分类模型中设置过获取器
        // 查询商品所属的一级分类下 所有二级分类
        $cate_two=\app\common\model\Category::where('pid',$pid_path[1])->select();
        // 查询商品所属的二级分类下的 所有的三级分类
        $cate_three=\app\common\model\Category::where('pid',$pid_path[2])->select();

        // 返回数据
        $data=[
          'goods'=>$goods,
           'type'=>$type,
           'category'=>[
                'cate_one'=>$cate_one,
                'cate_two'=>$cate_two,
                'cate_three'=>$cate_three,
           ]
        ];
        $this->ok($data);
    }
    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        // 接收参数
        $params=input();
        // 验证参数验证
        $rule=[
            'goods_name|商品名称' => 'require|max:100',
            'goods_price|商品价格' => 'require|float|gt:0',
            'goods_number|商品库存' => 'require|integer|gt:0',
//            'goods_logo|商品logo' => 'require',
            'goods_images|商品相册' => 'array',
            'item|规格值' => 'require|array',
            'attr|属性值' => 'require|array',
            'type_id|商品模型' => 'require',
            'brand_id|商品品牌' => 'require',
            'cate_id|商品分类' => 'require',
        ];
        // 参数检测
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->fail($validate,400);
        }
        // 添加数据
        // 开启事务
        \think\Db::startTrans();
        try{
            // logo图片生成缩略图
            if(!empty($params['goods_logo']) && is_file('.'.$params['goods_logo'])){
                // 重新取名字
                $goods_logo=dirname($params['goods_logo']). DS.'thumb_'.basename($params['goods_logo']);
                // 制作缩略图
                \think\Image::open('.'.$params['goods_logo'])->thumb('200',240)->save('.'.$goods_logo);
                $params['goods_logo']=$goods_logo;
            }
            // 因为接收到属性是一个数组 数组是不能存入数据库的 所以进行转换
            $params['goods_attr']=json_encode($params['goods_attr'],JSON_UNESCAPED_UNICODE);
            // 修改商品表数据
            \app\common\model\Goods::update($params,[],true);

            // 商品相册表数据
            if(!empty($params['goods_images'])) {
                $goods_images_data = [];
                foreach ($params['goods_images'] as $k => $v) {
                    if (!is_file('.' . $v)) {
                        continue;
                    }
                    //  $v就是一个图片地址 需要生成两张不同尺寸的缩略图 组装成一条数组
                    $pics_big = dirname($v) . DS . 'thumb_800_' . basename($v);
                    $pics_sma = dirname($v) . DS . 'thumb_400_' . basename($v);
                    // 生成缩略图
                    $images=\think\Image::open('.'.$v);
                    $images->thumb(800,800)->save($pics_big);
                    $images->thumb(400,400)->save($pics_sma);
                    $goods_images_data[]=[
                      'goods_id'=>$id,
                      'pics_big'=>$pics_big,
                      'pics_sma'=>$pics_sma,
                    ];
                }
                $goods_images=new \app\common\model\GoodsImages();
                $goods_images->saveAll($goods_images_data);
            }

            // 规格商品表数据 sku表
            // 先删除原来的数据再添加数据
            \app\common\model\SpecGoods::destroy(['goods_id'=>$id]);
            $spec_goods_data=[];
            foreach($params['item'] as $v){
                $v['goods_id']=$id;
                $spec_goods_data[]=$v;
            }
            $spec_goods_model= new \app\common\model\SpecGoods();
            $spec_goods_model->allowField(true)->saveAll($spec_goods_data);
            // 提交事务
            \think\Db::commit();
            // 查出数据 返回数据
            $info=\app\common\model\Goods::with('type_bind,brand_bind,category_bind')->find($id);
            $this->ok($info);

        }catch(\Exception $e){
            \think\Db::rollback();
            $this->fail("操作失败");
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //如果商品下有商品 就不能删除 此条数据 先查出来
        $is_no_sale= \app\common\model\Goods::where('id','=',$id)->value('is_on_sale');
        if($is_no_sale){
            $this->fail("商品已经上架,不能删除");
        }
        // 删除数据  如果 is_on_sale 为true 就不能删除 那么为false 就能删除
        \app\common\model\Goods::destroy($id);
        // 删除图片
        $goods_images= \app\common\model\GoodsImages::where('goods_id',$id)->select();
        \app\common\model\GoodsImages::destroy(['goods_id'=>$id]);

        $images=[];
        foreach($goods_images as $v){
            $images[]=$v['pics_big'];
            $images[]=$v['pics_sma'];
        }
        foreach ($images as $v){
            if(is_file('.'.$v)){
                unlink('.'.$v);
            }
        }
        $this->ok();
    }

    // 删除相册图片接口
    public function delpics($id){
        // 从数据表删除数据
        $data= \app\common\model\GoodsImages::find($id);
        if(!$data){
            $this->ok();
        }
        $data->delete();
        // 磁盘删除数据
        if(is_file('.'.$data['pics_big'])){
            unlink('.'.$data['pics_big']);
        }
        if(is_file('.'.$data['pics_sma'])){
            unlink('.'.$data['pics_sma']);
        }
    }
}
