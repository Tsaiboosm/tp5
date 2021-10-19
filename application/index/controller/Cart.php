<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Cart as CartModel;
use app\index\model\Goods as GoodsModel;
class Cart extends Controller {
  
    // 购物车显示
    public function index() {
        // 未登录不允许操作
        if (empty(session('member'))) {
            $this->error('请先登录', 'member/login');
        }
        
        $model = new CartModel();
        //var_dump(session('member'));exit();
        $itemList = $model->addbaos(session('member.id'));
        $itemCount = $model->goodsItemCount(session('member.id'));
        
        
        $this->assign('itemList', $itemList);
        $this->assign('itemCount', $itemCount);
        return $this->fetch('cart_list');
    }
    public function indexca() {
        if (empty(session('member'))) {
            $this->error('请先登录', 'member/login');
        }
        $model = new CartModel();
        //var_dump(session('member'));exit();
        $itemList = $model->addbaos(session('member.id'));
        $itemCount = $model->goodsItemCount(session('member.id'));
        
        $this->assign('itemList', $itemList);
        $this->assign('itemCount', $itemCount);
        return $this->fetch('indexca');
    }
    
    // 删除购物车中的某个商品
    public function deleteOne() {
        if (empty(session('member')) || !request()->isAjax()) {
            return;
        }
        
        // 有一个安全bug。假如有人登录了自己的账号，修改前端页面的cartId，可以删除别人购物车的商品。
        
        $cartId = input('post.cartId');
        $item = CartModel::get($cartId);
        $res['status'] = true;
        if ($item->memberId != session('member.id')) { // 企图删除他人购物车中的商品
            $res['status'] = false;
            $res['msg'] = "您无权限操作";
        } else if ($item->delete() != 1) {
            $res['status'] = false;
            $res['msg'] = "删除失败" . $item->getError();
        }
        
        // 如果删除成功，将用户购物车中的商品数目返回
        if ($res['status']) {
            $model = new CartModel();
            $res['data'] = $model->goodsItemCount(session('member.id'));
        }

        return json($res);
    }
    
    public function deleteMore() {
        if (empty(session('member')) && !request()->isAjax()) {
            return;
        }
        
        $cartIds = input('post.cartIds/a');
        $items = CartModel::where('id', 'in', $cartIds)->select();
        
        $res['status'] = true;
        foreach($items as $item) {
            if (empty($item) || $item->memberId != session('member.id') || $item->delete() != 1) {
                $res['status'] = false;
                $res['msg'] = '部分删除失败';
                return json($res);
            }
        }
        
        // 如果删除成功，将用户购物车中的商品数目返回
        if ($res['status']) {
            $model = new CartModel();
            $res['data'] = $model->goodsItemCount(session('member.id'));
        }
        
        return json($res);
    }
    
    // ajax更新购物车某个商品的购买数量
    public function updateNum() {
        if (empty(session('member')) && !request()->isAjax()) {
            return;
        }

        $res['status'] = true;
        
        $cartId = input('post.cartId');
        $count = input('post.count');

        if (!is_numeric($count)) {
            $res['status'] = false;
            $res['msg'] = '参数非法，数量不是纯数字';
            return json($res);
        }
        
        $item = CartModel::get($cartId);
        
        // 企图修改他人购物车中的商品
        if ($item->memberId != session('member.id')) {
            $res['status'] = false;
            $res['msg'] = "您无权限操作";
            return json($res);
        }

        // 判断是否超出库存
        $goods = GoodsModel::get($item->goodsId);

        if ($count > $goods->count) {
            $res['status'] = false;
            $res['msg'] = '库存不足，剩余 ' . $goods->count . '件';
            return json($res);
        }
            
        $item->count = $count;
        if (!$item->save()) { // 新的值和旧的值一样，save不会修改
            $res['status'] = false;
            $res['msg'] = '请修改不同的数量';
        }
        return json($res);
    }
    
    // 异步ajax往购物车里面添加商品
    public function add() { 
        //return json(input('post.'));

        // 未登录不允许操作
        if (empty(session('member'))) {
            $res['status'] = false;
            $res['msg'] = '请先登录';
            return json($res);
        }
        
        $memberId= session('member.id');
        $goodsId = input('post.goodsId'); // 商品id
        $count = input('post.count');  // 添加到购物车中的数量
        
        $goods = GoodsModel::get($goodsId);
        if (empty($goods)) {
            $res['status'] = false;
            $res['msg'] = '商品不存在';
            return json($res);
        }
        if ($goods->onSale == 0) {
            $res['status'] = false;
            $res['msg'] = '商品已被下架';
            return json($res);
        }
        
        $model = new CartModel();
        $res['status'] = true;
        $item = $model->findOne($memberId, $goodsId);
        
        // 购物车中没有
        if (empty($item)) {
            $item = $model->add($memberId, $goodsId, $count);
            if (empty($item)) { // 添加失败
                $res['status'] = false;
                $res['msg'] = $item->getError();
            } 
        } else {
            $goods = GoodsModel::get($goodsId);
            if ($item->count >= $goods->count) {
                $res['status'] = false;
                $res['msg'] = '购物车中的商品数量超出现有库存';
            } else {
                // 该记录的'count'字段+1
                if (!$item->setInc('count', $count)) {
                    $res['status'] = false;
                    $res['msg'] = $item->getError();
                }
            }
        }
        
        // 如果添加成功，将用户购物车中的商品数目返回
        if ($res['status']) {
            $res['data'] = $model->goodsItemCount($memberId);
        }
        
        return json($res);
    }
    //附推荐喜欢的商品
   
}
?>
