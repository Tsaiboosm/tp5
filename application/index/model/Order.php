<?php
namespace app\index\model;
use think\Model;
use think\db\Query;

class Order extends Model {
    // 个人用户未收货订单数量
    public function goodsItemCount($memberId) {
        return Order::where(['memberId'=>$memberId , 'status'=>2])->count();
       
    }
    // 个人用户未评论数量
    public function goodsItemplun($memberId) {
        return Order::where(['memberId'=>$memberId , 'status'=>3])->count();
       
    }
    // 加入订单
    public function allOrderIds($memberId, $status, $period, $orderId, $params, $count = 2) {
        
        $query = new Query();
        $query = $query->table('order')->where('memberId', $memberId);
        if ($status != '') {
            $query = $query->where('status', intval($status));
        }
        if ($period != '') {
            $query = $query->whereTime('createTime', $period);
        }
        if ($orderId != '') {
            $query = $query->where('id', 'like', '%'.$orderId.'%');
        }
        $paginator = $query->field('id')->order('createTime desc')
                ->paginate($count, false, 
                    //['path'=>'javascript:submit([PAGE]);']);
                    ['query'=>$params]);
        return $paginator;
     }
  
    public function orderDetail($orderId) {
        return db('order')
        ->alias('o')
        ->where('o.id', $orderId)
        ->join('address a', 'o.addressId=a.id')
        ->field('o.id orderId, o.createTime, o.totalPay, o.payMethod,o.delivery, o.status, 
            a.consigneeName, a.mobilePhone, a.province, a.city, a.area, a.detail')
        ->find();
    }
    
}
?>
