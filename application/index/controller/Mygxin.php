<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Cart;
use app\index\model\Order;
use app\index\model\evaluate;
use app\index\model\Order as OrderModel;
use app\index\model\Cart as CartModel;
use app\index\model\OrderItem as OrderItemModel;
use app\index\model\Address as AddressModel;
use app\index\model\Member as MemberModel;
class Mygxin extends Controller
{
	
	
		public function index()
		{
			    // 某个用户的购物车中的商品种数
			$cartItemCount = 0;
			if (session('member')) {
				$cartModel = new Cart();
				$cartItemCount = $cartModel->goodsItemCount(session('member.id'));
			}
			//待收货数量
			$cartItem = 0;
			if (session('member')) {
				$cartModel = new Order();
				$cartItem = $cartModel->goodsItemCount(session('member.id'));
			}
			$cartItemplun = 0;
			if (session('member')) {
				$cartModel = new Order();
				$cartItemplun = $cartModel->goodsItemplun(session('member.id'));
			}
			// dump($cartItem);
			$this->assign('cartItemCount', $cartItemCount);
			$this->assign('cartItem', $cartItem);
			$this->assign('cartItemplun', $cartItemplun);
			return view();
	
		}
		public function remima(){
			
			
			return view();	
		}
		public function mygrxx(){
			if (empty(session('member'))) {
				$this->error('请先登录');
			}
		
			return view();
		}
		public function mygqxx(){

			if(empty(session('member'))){
				$this->error('请先登录!','member/login');
				//return '请先登录';
			}
			
			$status = input('status', '');
			$cond['status'] = $status;
			$period = input('period', '');
			$cond['period'] = $period;
			$orderId = input('orderId', '');
			$cond['orderId'] = $orderId;
			
			$orderModel = new OrderModel();
			$paginator = $orderModel->allOrderIds(session('member.id'), $status, $period, $orderId, input());
			
			$orderIds = $paginator->items();
			$pageHtml = $paginator->render();
			
			$itemModel = new OrderItemModel();
			
			$res = array();
			foreach ($orderIds as $orderId) {
				$id = $orderId['id'];
				$orderDetail = $orderModel->orderDetail($id);
				$itemList = $itemModel->allOrderItems($id);
				
				$res[$id] = array($orderDetail, $itemList);
			}
			// dump($res);
			$this->assign('orderList', $res);
			$this->assign('pageHtml', $pageHtml);
			$this->assign('cond', $cond);
			$this->assign('cartItemsCount', CartModel::count());
			
			$model = new CartModel();
			//var_dump(session('member'));exit();
			$itemCount = $model->goodsItemCount(session('member.id'));
			$this->assign('cartItemCount', $itemCount);
			
			return $this->fetch('mygqxx');
			
		}
		public function doLeaveword()
		{
			$data = input('post.');
		 if (!captcha_check($data["verifyCode"])) {
				return json([
					"status" => "failed",
					"msg" => "验证码错误",
				]);
			}
			
			$leaveword = new evaluate();
			$leaveword->data(["user_id"=>session('member.id'),"time"=>date("y-m-d H:i:s"),"goods_id"=>input('post.goodsId'),"title"=>$data['title'],"content"=>$data['content']])->save();
			dump($leaveword);
			if ($leaveword) {
				return $this->success('留言成功!');
			}
			return view();
		}
		public function addzi(){
			// 地址数量
			if (empty(session('member'))) {
				$this->error('请先登录');
			}
		
			$this->assign('allAddress', $this->allAddress());
			return view();
		}
		 // 删除一个地址0
		 public function deleteAddress() {
			if (empty(session('member'))) {
				return;
			}
			
			$addressId = input("post.addressId");
			$address = AddressModel::get($addressId);
			
			$res['status'] = true;
			// 不是本人，没有权限操作
			if ($address->memberId != session('member.id')) {
				$res['status'] = false;
				$res['msg'] = '您没有权限操作';
			}
			
			if ($address->delete() != 1) {
				$res['status'] = false;
				$res['msg'] = $address->getError();
			}
			
			return json($res);
			
		}
		public function allAddress() {
        
			$addrs = AddressModel::where('memberId', session('member.id'))
			->order('id', 'desc')->select();
			// dump($addrs);
			 $html = '';
			foreach ($addrs as $addr) {
				$html .= $this->addressHtml($addr->consigneeName, $addr->mobilePhone, $addr->telephone,
					$addr->province, $addr->city, $addr->area, $addr->detail, $addr->id);
			}
			
			return  $html;
		}
		function addressHtml($consigneeName, $mobilePhone, $telephone, 
        $province, $city, $area, $detail, $addressId) {
       $html = "
       <div  style='width: 244px;  height: 158px; border: 1px solid #E0E0E0;padding: 20px 0 0 24px; color: #757575;line-height: 20px;'onClick='selectAddress(this, {$addressId})'>
	   <p >用户名:{$consigneeName}</p>
		   <a href=javascript:void(0) class=delete onClick='deleteAddress({$addressId}, this)'>删除</a>
           <p>地址:{$province}&nbsp;&nbsp;备注:{$area}&nbsp;&nbsp;&nbsp;</p>
           <p>详细地址:{$detail}&nbsp;&nbsp;邮编:{$city} </p>
           <p>手机号:{$mobilePhone}</p>
      
   </div>
";
            // $html = "  <li  onClick='selectAddress(this, {$addressId})' class='list-group-item list-group-item-action'>
            
            //    <span class=username>{$consigneeName}&nbsp;</span>
            
            //     <span class=item-address> {$province}&nbsp;
            // {$city}&nbsp;
            // {$area} &nbsp;
            // {$detail}&nbsp;</span>  
            //  <span class=contact>{$mobilePhone}</span>
             
            // </li>";
        return $html;
    }




// 修改用户
	public function save(){
		$index_id = session('member.id');
			$data = input('post.');
			$member = new MemberModel();
			$index_info = $member->where('id', $index_id)->find();
			if ($data) {
			$index_id = session('member.id');
			
			if ($data['email'] == $index_info['email']) {
			
							$email =($data['email']);
							$memberName =($data['memberName']);
							$gender =($data['gender']);
							if($gender == ''){
								return $this->error('上传文件不能为空!');
							}
					
						$result = MemberModel::where('id', $index_id)->update(['memberName' => $memberName,'gender' => $gender,'email' => $email,'timeto'=>date("y-m-d H:i:s")]);
						
						if ($result) {
							return $this->success('修改成功啦!');
						} else {

							return $this->error('修改失败');
						}
				
					} else {
						return $this->error('邮箱错误!');
					}
				
			}

	}
	// 图片上传
	public function upload_img(){
		$file = request()->file('file');
		if($file==null){
			exit(json_encode(array('code'=>1,'msg'=>'没有文件上传')));
		}
		$info = $file->move(ROOT_PATH.'public'.DS.'uploads');
		$ext = ($info->getExtension());
		if(!in_array($ext,array('jpg','jpeg','gif','png'))){
			exit(json_encode(array('code'=>1,'msg'=>'文件格式不支持')));
		}
		$img = '/uploads/'.$info->getSaveName();
		exit(json_encode(array('code'=>0,'msg'=>$img)));
	}

		public function passwordedit()
		{
			$index_id = session('member.id');
			$data = input('post.');
			$member = new MemberModel();
			$index_info = $member->where('id', $index_id)->find();
			if ($data) {
			$index_id = session('member.id');
				if ((md5($data['password_origin'])) == $index_info['password']) {
				
					if ($data['password'] == $data['password_again']) {
						if ($data['email'] == $index_info['email']) {
							$email =($data['email']);
						$password = md5($data['password']);
						$result = MemberModel::where('id', $index_id)->update(['password' => $password,'email' => $email,'timeto'=>date("y-m-d H:i:s")]);
						
						if ($result) {
							return $this->success('修改成功啦!','http://cai:83/index/member/logout');
						} else {

							return $this->error('修改失败');
						}
					}else {
						return $this->error('邮箱错误');
					}
					} else {
						return $this->error('再次输入不一致!');
					}
				} else {
					return $this->error('原密码输入错误!');
					
				}
			}
			return view();
		}
	


	}
