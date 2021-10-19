<?php
namespace app\index\model;
use think\Model;

class Cart extends Model {
    
    public function goods() {
        //return $this->belongsTo('goods', 'goodsId');
        return '模型关联';
    }
    
    // 某个用户的购物车中的商品种数
    public function goodsItemCount($memberId) {
        return Cart::where([
            'memberId' => $memberId
        ])->count();
        
    }
 
    
    // 需要计算费用小计
    public function findOneNeedPrice($cartId, $memberId) {
        return db('cart')
        ->alias('c')
        ->join('goods g', 'c.goodsId=g.id')
        ->where(['c.id'=>$cartId, 'c.memberId'=>$memberId])
        // last：商品库存
        ->field('c.*, g.salePrice')
        ->find();
    }
    
    public function selectByIds($cartIds) {
        return db('cart')
        ->alias('c')
        ->join('goods g', 'c.goodsId=g.id')
        ->where('c.id', 'in', $cartIds)
        // last：商品库存
        ->field('c.id as cartId, c.count, g.id as goodsId, goodsName, marketPrice, mainPic, salePrice, g.count as last')
        ->select();
    }
    
    // 获取某个用户的购物车内容
    public function addbaos($memberId) {
        return db('cart')
        ->alias('c')
        ->join('goods g', 'c.goodsId=g.id')
        ->where('memberId', $memberId)
        // last：商品库存
        ->field('c.id as cartId, c.count, g.id as goodsId, goodsName, mainPic, salePrice, g.count as last')
        ->select();
    }
    
    // 返回一个Model对象，而不是数组
    public function findOne($memberId, $goodsId) {
        return Cart::where([
            'memberId' => $memberId,
            'goodsId' => $goodsId
        ])->find();
    }
    
    // 插入一条数据，返回Model对象
    public function add($memberId, $goodsId, $count) {
        $item = new Cart();
        $item->memberId = $memberId;
        $item->goodsId = $goodsId;
        $item->count = $count;
        //return $item;
        if ($item->save()) { 
            return $item; // 添加成功，返回一个Model对象
        } else {
            return null;
        }
    }
    
    
    
}
?>
