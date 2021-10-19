<?php
namespace app\index\model;
use think\Model;

class evaluate extends Model {
    public function allevaluateItems($evaluate) {
        return db('evaluate')
        ->alias('e')
        ->where('e.evaluate', $evaluate)
        ->join('goods g', 'o.goods_id=g.id')
        ->field('g.mainPic, g.goodsName, g.salePrice, e.content, e.content, e.goods_id')
        ->select();
    }
    // public function CartItemCount($memberId) {
    //     return Address::where([
    //         'memberId' => $memberId
    //     ])->count();
        
    // }
}
?>
