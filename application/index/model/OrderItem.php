<?php
namespace app\index\model;
use think\Model;

// 必须注意！！！OrderItem，驼峰命名法，它会去找表名为：order_item的表
class OrderItem extends Model {
    protected $table = 'orderitem';
    
    public function allOrderItems($orderId) {
        return db('orderitem')
        ->alias('o')
        ->where('o.orderId', $orderId)
        ->join('goods g', 'o.goodsId=g.id')
        ->field('g.mainPic, g.goodsName, g.salePrice, o.count, o.goodsId')
        ->select();
    }
}
?>
